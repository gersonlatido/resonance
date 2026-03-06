<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;

class TableController extends Controller
{
    // Tables that can be shared (groups)
    private array $shareGroups = [
        [7, 8, 9],
        [3, 4],
    ];

    public function enter(Request $request, int $table)
    {
        if ($table < 1 || $table > 10) {
            abort(404);
        }

        // ✅ BLOCK IMMEDIATELY if scanned table is unavailable
        $scannedRow = Table::where('number', $table)->first();

        if ($scannedRow && !$scannedRow->is_available) {
            return view('table-unavailable', [
                'table' => (int) $table,
            ]);
        }

        /**
         * ✅ Show loading screen for valid tables
         * Redirect back to same URL with ?go=1 after 3 seconds
         */
        if (!$request->boolean('go')) {
            $redirectUrl = route('table.enter', ['table' => $table]) . '?go=1';

            return view('table-loading', [
                'table' => (int) $table,
                'redirectUrl' => $redirectUrl,
            ]);
        }

        // Find share group
        $group = $this->findGroup($table);

        // Not shareable → old behavior
        if (!$group) {
            session([
                'table_number'  => (int) $table,
                'table_numbers' => [(int) $table],
                'table_label'   => 'Table ' . (int) $table,
            ]);

            return redirect('/');
        }

        // Share group → show choose-table
        $tables = Table::whereIn('number', $group)->get()->keyBy('number');

        $availability = [];
        foreach ($group as $n) {
            $availability[$n] = isset($tables[$n]) ? (bool) $tables[$n]->is_available : true;
        }

        return view('choose-table', [
            'scanned'      => (int) $table,
            'group'        => $group,
            'availability' => $availability,
        ]);
    }

    public function select(Request $request)
    {
        $data = $request->validate([
            'scanned'    => 'required|integer|min:1|max:10',
            'mode'       => 'required|in:solo,shared',
            'partners'   => 'nullable|array',
            'partners.*' => 'integer|min:1|max:10',
        ]);

        $scanned  = (int) $data['scanned'];
        $mode     = $data['mode'];
        $partners = array_values(array_unique(array_map('intval', $data['partners'] ?? [])));

        $group = $this->findGroup($scanned);

        // If not in a share group, fallback to solo session
        if (!$group) {
            session([
                'table_number'  => $scanned,
                'table_numbers' => [$scanned],
                'table_label'   => 'Table ' . $scanned,
            ]);

            return redirect('/');
        }

        // Pull availability from DB (so no cheating)
        $tables = Table::whereIn('number', $group)->get()->keyBy('number');
        $isAvail = fn (int $n) => isset($tables[$n]) ? (bool) $tables[$n]->is_available : true;

        // ✅ scanned must be available
        if (!$isAvail($scanned)) {
            return view('table-unavailable', [
                'table' => $scanned,
            ]);
        }

        // SOLO
        if ($mode === 'solo') {
            session([
                'table_number'  => $scanned,
                'table_numbers' => [$scanned],
                'table_label'   => 'Table ' . $scanned,
            ]);

            return redirect('/');
        }

        // SHARED (multi select)
        $partners = array_values(array_filter($partners, fn ($n) => $n !== $scanned));

        if (count($partners) < 1) {
            return back()->with('error', 'Please pick at least one table to share with.');
        }

        // partners must be inside same group and available
        foreach ($partners as $p) {
            if (!in_array($p, $group, true)) {
                return back()->with('error', "Table {$p} is not in your share group.");
            }

            if (!$isAvail($p)) {
                return back()->with('error', "Table {$p} is unavailable. Choose another.");
            }
        }

        $tablesChosen = array_merge([$scanned], $partners);
        sort($tablesChosen);

        session([
            'table_number'  => $tablesChosen[0], // keep compatibility
            'table_numbers' => $tablesChosen,    // real list
            'table_label'   => 'Shared Table ' . implode(' & ', $tablesChosen),
        ]);

        return redirect('/');
    }

    /**
     * ✅ Customer clicks "Done Eating" to set table(s) available again.
     */
    public function doneEating(Request $request)
    {
        $tables = session('table_numbers');

        if (!is_array($tables) || count($tables) === 0) {
            $single = session('table_number');
            $tables = $single ? [(int) $single] : [];
        }

        if (count($tables) === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'No table found in session.'
            ], 400);
        }

        $orderCode = session('order_code');
        $hasPaid = false;

        if ($orderCode) {
            $hasPaid = Order::where('order_code', (string) $orderCode)
                ->where('payment_status', 'paid')
                ->exists();
        }

        if (!$hasPaid) {
            $hasPaid = Order::whereIn('table_number', array_map('intval', $tables))
                ->where('payment_status', 'paid')
                ->exists();
        }

        if (!$hasPaid) {
            return response()->json([
                'ok' => false,
                'message' => 'No paid order found for this table.'
            ], 403);
        }

        Table::whereIn('number', array_map('intval', $tables))
            ->update(['is_available' => true]);

        return response()->json([
            'ok' => true,
            'message' => 'Table is now available.',
            'tables' => array_map('intval', $tables),
        ]);
    }

    private function findGroup(int $table): ?array
    {
        foreach ($this->shareGroups as $g) {
            if (in_array($table, $g, true)) {
                return $g;
            }
        }

        return null;
    }
}