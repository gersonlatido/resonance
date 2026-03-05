<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesStockReportController extends Controller
{
    public function index(Request $request)
    {
        [$data, $meta] = $this->buildStockData($request, true);
        return view('admin.reports.sales-stock-report', array_merge($meta, $data));
    }

    public function print(Request $request)
    {
        [$data, $meta] = $this->buildStockData($request, false);

        $payload = array_merge($meta, $data, [
            'generatedAt' => now()->format('Y-m-d h:i A'),
            'type' => 'stock',
        ]);

        return view('admin.reports.sales-stock-report-print-stock', $payload);
    }

    public function exportCsv(Request $request)
    {
        [$data, $meta] = $this->buildStockData($request, false);

        $rangeLabel = $meta['rangeLabel'];
        $start      = $meta['start'];
        $end        = $meta['end'];

        $from   = $meta['from'];
        $to     = $meta['to'];
        $q      = $meta['q'];
        $status = $meta['status'];

        $ingredients    = $data['ingredients'];     // Collection
        $lowStockCount  = $data['lowStockCount'];

        $movements      = $data['movements'];       // Collection
        $movementCount  = $data['movementCount'];

        $consumedRows   = $data['consumedRows'];    // Collection rows: name, unit, qty_used
        $consumedCount  = $data['consumedCount'];
        $consumedTotal  = $data['consumedTotal'];

        $filename = "stock_report_" . $start->format('Ymd') . "_to_" . $end->format('Ymd') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use (
            $rangeLabel, $start, $end,
            $from, $to, $q, $status,
            $ingredients, $lowStockCount,
            $movements, $movementCount,
            $consumedRows, $consumedCount, $consumedTotal
        ) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM

            fputcsv($out, ['99 SILOG CAFE']);
            fputcsv($out, ['STOCK REPORT']);
            fputcsv($out, ['']);
            fputcsv($out, ['Range Label', $rangeLabel]);
            fputcsv($out, ['From', $from ?: '(auto)']);
            fputcsv($out, ['To', $to ?: '(auto)']);
            fputcsv($out, ['Search', $q !== '' ? $q : '(none)']);
            fputcsv($out, ['Status Filter', strtoupper($status)]);
            fputcsv($out, ['Start', $start->format('Y-m-d H:i:s')]);
            fputcsv($out, ['End', $end->format('Y-m-d H:i:s')]);
            fputcsv($out, ['Generated', now()->format('Y-m-d h:i A')]);
            fputcsv($out, ['Low/Out Items (filtered)', $lowStockCount]);
            fputcsv($out, ['Movement Rows (filtered)', $movementCount]);
            fputcsv($out, ['Consumed Items', $consumedCount]);
            fputcsv($out, ['Total Consumed Qty (sum of OUT)', number_format((float)$consumedTotal, 2)]);
            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Inventory Snapshot
            fputcsv($out, ['INVENTORY SNAPSHOT (FILTERED, CURRENT STOCK)']);
            fputcsv($out, ['Ingredient', 'Unit', 'Stock', 'Reorder Level', 'Status']);
            foreach ($ingredients as $ing) {
                $stock   = (float) ($ing->stock_qty ?? 0);
                $reorder = (float) ($ing->reorder_level ?? 0);
                $rowStatus = ($stock <= 0) ? 'OUT' : (($stock <= $reorder) ? 'LOW' : 'OK');

                fputcsv($out, [
                    (string) $ing->name,
                    (string) $ing->unit,
                    number_format($stock, 2),
                    number_format($reorder, 2),
                    $rowStatus,
                ]);
            }

            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Consumed Summary
            fputcsv($out, ['INGREDIENTS CONSUMED SUMMARY (SUM OF OUT IN RANGE)']);
            fputcsv($out, ['Ingredient', 'Unit', 'Qty Used']);
            foreach ($consumedRows as $r) {
                fputcsv($out, [
                    (string)($r->ingredient_name ?? ''),
                    (string)($r->unit ?? ''),
                    number_format((float)($r->qty_used ?? 0), 2),
                ]);
            }

            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Movements
            fputcsv($out, ['STOCK MOVEMENTS (HISTORY, FILTERED BY DATE RANGE)']);
            fputcsv($out, ['Date', 'Ingredient', 'Type', 'Qty', 'Reason']);
            foreach ($movements as $m) {
                $type = strtoupper((string)($m->type ?? ''));
                $qty  = (float)($m->qty ?? 0);
                $date = optional($m->created_at)->format('Y-m-d h:i A');

                fputcsv($out, [
                    $date,
                    (string) optional($m->ingredient)->name,
                    $type,
                    number_format($qty, 2),
                    (string)($m->reason ?? ''),
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildStockData(Request $request, bool $paginate): array
    {
        // Date range (used for movements + consumed summary)
        $from = trim((string) $request->query('from', ''));
        $to   = trim((string) $request->query('to', ''));

        // Default range: last 7 days
        if ($from === '' && $to === '') {
            $start = Carbon::today()->subDays(6)->startOfDay();
            $end   = Carbon::today()->endOfDay();
            $rangeLabel = "Last 7 days (" . $start->format('M d, Y') . " - " . $end->format('M d, Y') . ")";
        } else {
            $fromDate = $from !== '' ? Carbon::parse($from)->startOfDay() : Carbon::parse($to)->startOfDay();
            $toDate   = $to !== ''   ? Carbon::parse($to)->endOfDay()     : Carbon::parse($from)->endOfDay();
            $start = $fromDate;
            $end   = $toDate;
            $rangeLabel = "Custom (" . $start->format('M d, Y') . " - " . $end->format('M d, Y') . ")";
        }

        // Ingredient filters
        $q = trim((string) $request->query('q', ''));
        $status = strtolower((string) $request->query('status', 'all')); // all|ok|low|out

        $ingredientQuery = Ingredient::query();

        if ($q !== '') {
            $ingredientQuery->where('name', 'like', "%{$q}%");
        }

        if ($status === 'out') {
            $ingredientQuery->where('stock_qty', '<=', 0);
        } elseif ($status === 'low') {
            $ingredientQuery->where('stock_qty', '>', 0)
                ->whereColumn('stock_qty', '<=', 'reorder_level');
        } elseif ($status === 'ok') {
            $ingredientQuery->where('stock_qty', '>', 0)
                ->whereColumn('stock_qty', '>', 'reorder_level');
        } else {
            $status = 'all';
        }

        $ingredientQuery->orderBy('name');

        $filteredTotal = (clone $ingredientQuery)->count();

        $lowStockCount = (clone $ingredientQuery)->where(function ($qq) {
            $qq->where('stock_qty', '<=', 0)
               ->orWhereColumn('stock_qty', '<=', 'reorder_level');
        })->count();

        // Ingredients data
        if ($paginate) {
            $ingredients = $ingredientQuery->paginate(10, ['*'], 'inv_page')->withQueryString();
        } else {
            $ingredients = $ingredientQuery->get();
        }

        // Movements (history)
        $movementQuery = StockMovement::with('ingredient')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $movementQuery->whereHas('ingredient', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%");
            });
        }

        $movementCount = (clone $movementQuery)->count();

        if ($paginate) {
            $movements = $movementQuery->paginate(10, ['*'], 'mv_page')->withQueryString();
        } else {
            $movements = $movementQuery->take(500)->get();
        }

        /**
         * ✅ NEW: Consumed Summary
         * Sum ONLY type = 'out' within range
         */
        $consumedBase = StockMovement::query()
            ->join('ingredients', 'ingredients.id', '=', 'stock_movements.ingredient_id')
            ->where('stock_movements.type', 'out')
            ->whereBetween('stock_movements.created_at', [$start, $end]);

        // apply search too (same q)
        if ($q !== '') {
            $consumedBase->where('ingredients.name', 'like', "%{$q}%");
        }

        $consumedRowsQuery = (clone $consumedBase)
            ->select([
                DB::raw('ingredients.name as ingredient_name'),
                DB::raw('ingredients.unit as unit'),
                DB::raw('SUM(stock_movements.qty) as qty_used'),
            ])
            ->groupBy('ingredients.name', 'ingredients.unit')
            ->orderByDesc(DB::raw('SUM(stock_movements.qty)'));

        $consumedCount = (clone $consumedRowsQuery)->get()->count(); // grouped rows count
        $consumedTotal = (clone $consumedBase)->sum('stock_movements.qty');

        if ($paginate) {
            // Unique page name so it won't conflict
            $consumedRows = $consumedRowsQuery->paginate(10, ['*'], 'use_page')->withQueryString();
        } else {
            $consumedRows = $consumedRowsQuery->get();
        }

        $data = [
            'ingredients'    => $ingredients,
            'filteredTotal'  => $filteredTotal,
            'lowStockCount'  => $lowStockCount,

            'movements'      => $movements,
            'movementCount'  => $movementCount,

            'consumedRows'   => $consumedRows,
            'consumedCount'  => $consumedCount,
            'consumedTotal'  => $consumedTotal,
        ];

        $meta = [
            'period'     => 'custom',
            'rangeLabel' => $rangeLabel,
            'start'      => $start,
            'end'        => $end,

            'from'       => $from,
            'to'         => $to,

            'q'          => $q,
            'status'     => $status,
        ];

        return [$data, $meta];
    }
}