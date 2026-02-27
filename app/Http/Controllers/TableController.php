<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    // Tables that can be shared (groups)
    private array $shareGroups = [
        [7, 8, 9],
        [3, 4],
    ];

    public function enter(int $table)
    {
        if ($table < 1 || $table > 10) abort(404);

        // ✅ NEW RULE: cannot scan an unavailable table
        $scannedRow = Table::where('number', $table)->first();
        if ($scannedRow && !$scannedRow->is_available) {
            return redirect('/')->with('error', "Table {$table} is unavailable.");
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

        // Load availability for group (for UI display only)
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
        $isAvail = fn(int $n) => isset($tables[$n]) ? (bool) $tables[$n]->is_available : true;

        // ✅ scanned must be available (since you said they cannot scan unavailable)
        if (!$isAvail($scanned)) {
            return back()->with('error', "Table {$scanned} is unavailable.");
        }

        // SOLO
        if ($mode === 'solo') {
            // ✅ IMPORTANT: do NOT mark unavailable here (only after payment)
            session([
                'table_number'  => $scanned,
                'table_numbers' => [$scanned],
                'table_label'   => 'Table ' . $scanned,
            ]);
            return redirect('/');
        }

        // SHARED (multi select)
        // remove scanned if somehow included
        $partners = array_values(array_filter($partners, fn($n) => $n !== $scanned));

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

        // ✅ IMPORTANT: do NOT mark unavailable here (only after payment)
        $tablesChosen = array_merge([$scanned], $partners);
        sort($tablesChosen);

        session([
            'table_number'  => $tablesChosen[0], // keep compatibility
            'table_numbers' => $tablesChosen,    // real list
            'table_label'   => 'Shared Table ' . implode(' & ', $tablesChosen),
        ]);

        return redirect('/');
    }

    private function findGroup(int $table): ?array
    {
        foreach ($this->shareGroups as $g) {
            if (in_array($table, $g, true)) return $g;
        }
        return null;
    }
}