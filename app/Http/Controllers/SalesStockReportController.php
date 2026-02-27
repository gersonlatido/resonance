<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ingredient;
use Carbon\Carbon;

class SalesStockReportController extends Controller
{
    public function index(Request $request)
    {
        [$data, $meta] = $this->buildReportData($request);

        return view('admin.reports.sales-stock-report', array_merge($meta, $data));
    }

    /**
     * ✅ PRINT VIEW (user saves as PDF)
     */
   public function print(Request $request)
{
    $period = $request->query('period', 'daily');
    $selectedDate = $request->query('date');
    $type = $request->query('type', 'combined'); // combined | sales | stock

    $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();
    [$start, $end, $rangeLabel] = $this->getRangeByPeriod($period, $day);

    $paidOrders = Order::with('items')
        ->where('payment_status', 'paid')
        ->whereBetween('created_at', [$start, $end])
        ->orderByDesc('created_at')
        ->get();

    $totalSales = (float) $paidOrders->sum('total');
    $paidCount  = (int) $paidOrders->count();
    $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

    $topItems = $this->computeTopItems($paidOrders, 10);

    $ingredients = Ingredient::orderBy('name')->get();
    $lowStock = $ingredients->filter(function ($i) {
        return ($i->stock_qty <= 0) || ($i->stock_qty <= $i->reorder_level);
    })->values();

    $data = [
        'period'       => $period,
        'selectedDate' => $day->toDateString(),
        'rangeLabel'   => $rangeLabel,
        'generatedAt'  => now()->format('Y-m-d h:i A'),

        'paidOrders'   => $paidOrders,
        'totalSales'   => $totalSales,
        'paidCount'    => $paidCount,
        'avgOrder'     => $avgOrder,

        'topItems'     => $topItems,
        'ingredients'  => $ingredients,
        'lowStock'     => $lowStock,
        'type'         => $type,
    ];

    if ($type === 'sales') {
        return view('admin.reports.sales-stock-report-print-sales', $data);
    }

    if ($type === 'stock') {
        return view('admin.reports.sales-stock-report-print-stock', $data);
    }

    return view('admin.reports.sales-stock-report-print', $data); // combined
}

// public function exportCsv(Request $request)
// {
//     $period = $request->query('period', 'daily');
//     $selectedDate = $request->query('date');
//     $type = $request->query('type', 'combined'); // combined | sales | stock

//     $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();
//     [$start, $end, $rangeLabel] = $this->getRangeByPeriod($period, $day);

//     $paidOrders = Order::with('items')
//         ->where('payment_status', 'paid')
//         ->whereBetween('created_at', [$start, $end])
//         ->orderByDesc('created_at')
//         ->get();

//     $totalSales = (float) $paidOrders->sum('total');
//     $paidCount  = (int) $paidOrders->count();
//     $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

//     $topItems = $this->computeTopItems($paidOrders, 10);

//     $ingredients = Ingredient::orderBy('name')->get();
//     $lowStock = $ingredients->filter(function ($i) {
//         return ($i->stock_qty <= 0) || ($i->stock_qty <= $i->reorder_level);
//     })->values();

//     $filename = "sales_stock_report_{$type}_{$period}_" . $start->format('Ymd') . "_to_" . $end->format('Ymd') . ".csv";

//     $headers = [
//         'Content-Type'        => 'text/csv; charset=UTF-8',
//         'Content-Disposition' => 'attachment; filename="'.$filename.'"',
//     ];

//     $callback = function () use ($type, $period, $rangeLabel, $start, $end, $paidOrders, $totalSales, $paidCount, $avgOrder, $topItems, $ingredients, $lowStock) {
//         $out = fopen('php://output', 'w');
//         fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel ₱

//         fputcsv($out, ['99 SILOG CAFE']);
//         fputcsv($out, ['SALES & STOCK REPORT']);
//         fputcsv($out, ['Type', strtoupper($type)]);
//         fputcsv($out, ['Period', ucfirst($period)]);
//         fputcsv($out, ['Range', $rangeLabel]);
//         fputcsv($out, ['Start', $start->format('Y-m-d H:i:s')]);
//         fputcsv($out, ['End', $end->format('Y-m-d H:i:s')]);
//         fputcsv($out, ['Generated', now()->format('Y-m-d h:i A')]);
//         fputcsv($out, []);

//         if ($type === 'sales' || $type === 'combined') {
//             fputcsv($out, ['SALES SUMMARY']);
//             fputcsv($out, ['Total Sales (Paid)', '₱' . number_format($totalSales, 2)]);
//             fputcsv($out, ['Paid Orders', $paidCount]);
//             fputcsv($out, ['Average Order', '₱' . number_format($avgOrder, 2)]);
//             fputcsv($out, []);

//             fputcsv($out, ['BEST SELLERS']);
//             fputcsv($out, ['Item', 'Qty Sold']);
//             if (count($topItems) === 0) {
//                 fputcsv($out, ['(none)', 0]);
//             } else {
//                 foreach ($topItems as $it) {
//                     fputcsv($out, [(string)$it['name'], (int)$it['qty']]);
//                 }
//             }
//             fputcsv($out, []);

//             fputcsv($out, ['PAID ORDERS']);
//             fputcsv($out, ['Order Code', 'Table', 'Total', 'Status', 'Time']);
//             foreach ($paidOrders as $o) {
//                 fputcsv($out, [
//                     (string)$o->order_code,
//                     (string)$o->table_number,
//                     '₱' . number_format((float)$o->total, 2),
//                     (string)$o->status,
//                     optional($o->created_at)->format('Y-m-d h:i A'),
//                 ]);
//             }
//             fputcsv($out, []);
//         }

//         if ($type === 'stock' || $type === 'combined') {
//             fputcsv($out, ['INVENTORY SNAPSHOT']);
//             fputcsv($out, ['Ingredient', 'Unit', 'Stock', 'Reorder', 'Status']);

//             foreach ($ingredients as $ing) {
//                 $stock = (float)($ing->stock_qty ?? 0);
//                 $reorder = (float)($ing->reorder_level ?? 0);
//                 $status = ($stock <= 0) ? 'OUT' : (($stock <= $reorder) ? 'LOW' : 'OK');

//                 fputcsv($out, [
//                     (string)$ing->name,
//                     (string)$ing->unit,
//                     number_format($stock, 2),
//                     number_format($reorder, 2),
//                     $status
//                 ]);
//             }

//             fputcsv($out, []);
//             fputcsv($out, ['LOW STOCK COUNT', count($lowStock)]);
//         }

//         fclose($out);
//     };

//     return response()->stream($callback, 200, $headers);
// }

    /**
     * ✅ CSV DOWNLOAD (Excel-friendly)
     * includes: Sales summary, Best sellers, Low stock, Paid orders list
     */
    public function exportCsv(Request $request)
    {
        [$data, $meta] = $this->buildReportData($request);

        $period       = $meta['period'];
        $rangeLabel   = $meta['rangeLabel'];
        $selectedDate = $meta['selectedDate'];
        $start        = $meta['start'];
        $end          = $meta['end'];

        $totalSales    = $data['totalSales'];
        $paidCount     = $data['paidCount'];
        $avgOrder      = $data['avgOrder'];
        $paidOrders    = $data['paidOrders'];
        $topItems      = $data['topItems'];
        $lowStock      = $data['lowStock'];
        $lowStockCount = $data['lowStockCount'];

        $filename = "sales_stock_report_{$period}_" . $start->format('Ymd') . "_to_" . $end->format('Ymd') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use (
            $period, $rangeLabel, $selectedDate, $start, $end,
            $totalSales, $paidCount, $avgOrder,
            $topItems, $lowStock, $lowStockCount, $paidOrders
        ) {
            $out = fopen('php://output', 'w');

            // ✅ UTF-8 BOM for Excel (₱)
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($out, ['99 SILOG CAFE']);
            fputcsv($out, ['SALES & STOCK REPORT']);
            fputcsv($out, ['']);
            fputcsv($out, ['Period', ucfirst($period)]);
            fputcsv($out, ['Range', $rangeLabel]);
            fputcsv($out, ['Selected Date', $selectedDate]);
            fputcsv($out, ['Start', $start->format('Y-m-d H:i:s')]);
            fputcsv($out, ['End', $end->format('Y-m-d H:i:s')]);
            fputcsv($out, ['Generated', now()->format('Y-m-d h:i A')]);
            fputcsv($out, ['']);
            fputcsv($out, ['Total Sales (Paid)', '₱' . number_format($totalSales, 2)]);
            fputcsv($out, ['Paid Orders', $paidCount]);
            fputcsv($out, ['Average Order', '₱' . number_format($avgOrder, 2)]);
            fputcsv($out, ['Low Stock Items', $lowStockCount]);
            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Best sellers
            fputcsv($out, ['BEST-SELLING ITEMS']);
            fputcsv($out, ['Item', 'Qty Sold']);
            if (count($topItems) === 0) {
                fputcsv($out, ['(none)', 0]);
            } else {
                foreach ($topItems as $it) {
                    fputcsv($out, [(string)$it['name'], (int)$it['qty']]);
                }
            }

            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Low stock
            fputcsv($out, ['LOW STOCK INGREDIENTS']);
            fputcsv($out, ['Ingredient', 'Unit', 'Stock', 'Reorder Level', 'Status']);
            if (count($lowStock) === 0) {
                fputcsv($out, ['(none)', '', 0, 0, 'OK']);
            } else {
                foreach ($lowStock as $ing) {
                    $stock = (float) ($ing->stock_qty ?? 0);
                    $reorder = (float) ($ing->reorder_level ?? 0);
                    $status = ($stock <= 0) ? 'OUT' : 'LOW';

                    fputcsv($out, [
                        (string) $ing->name,
                        (string) $ing->unit,
                        number_format($stock, 2),
                        number_format($reorder, 2),
                        $status
                    ]);
                }
            }

            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            // Paid orders list
            fputcsv($out, ['PAID ORDERS LIST']);
            fputcsv($out, ['Order Code', 'Table', 'Total', 'Status', 'Paid Time']);
            foreach ($paidOrders as $order) {
                fputcsv($out, [
                    (string) $order->order_code,
                    (string) $order->table_number,
                    '₱' . number_format((float)$order->total, 2),
                    (string) $order->status,
                    optional($order->created_at)->format('Y-m-d h:i A'),
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ✅ One shared builder so index/print/export all match
     */
    private function buildReportData(Request $request): array
    {
        $period = $request->query('period', 'daily');
        $selectedDate = $request->query('date');
        $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();

        [$start, $end, $rangeLabel] = $this->getRangeByPeriod($period, $day);

        $paidOrders = Order::with('items')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalSales = (float) $paidOrders->sum('total');
        $paidCount  = (int) $paidOrders->count();
        $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

        $topItems = $this->computeTopItems($paidOrders, 10);

        $ingredients = Ingredient::orderBy('name')->get();

        $lowStock = $ingredients->filter(function ($i) {
            return ($i->stock_qty <= 0) || ($i->stock_qty <= $i->reorder_level);
        })->values();

        $lowStockCount = $lowStock->count();

        $data = [
            'paidOrders'     => $paidOrders,
            'totalSales'     => $totalSales,
            'paidCount'      => $paidCount,
            'avgOrder'       => $avgOrder,
            'topItems'       => $topItems,
            'ingredients'    => $ingredients,
            'lowStock'       => $lowStock,
            'lowStockCount'  => $lowStockCount,
        ];

        $meta = [
            'period'       => $period,
            'selectedDate' => $day->toDateString(),
            'rangeLabel'   => $rangeLabel,
            'start'        => $start,
            'end'          => $end,
        ];

        return [$data, $meta];
    }

    private function getRangeByPeriod(string $period, Carbon $day): array
    {
        $period = strtolower($period);

        if ($period === 'weekly') {
            $start = $day->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end   = $day->copy()->endOfWeek(Carbon::MONDAY)->endOfDay();
            $label = "Weekly (" . $start->format('M d, Y') . " - " . $end->format('M d, Y') . ")";
            return [$start, $end, $label];
        }

        if ($period === 'monthly') {
            $start = $day->copy()->startOfMonth()->startOfDay();
            $end   = $day->copy()->endOfMonth()->endOfDay();
            $label = "Monthly (" . $start->format('F Y') . ")";
            return [$start, $end, $label];
        }

        if ($period === 'yearly') {
            $start = $day->copy()->startOfYear()->startOfDay();
            $end   = $day->copy()->endOfYear()->endOfDay();
            $label = "Yearly (" . $start->format('Y') . ")";
            return [$start, $end, $label];
        }

        $start = $day->copy()->startOfDay();
        $end   = $day->copy()->endOfDay();
        $label = "Daily (" . $day->format('M d, Y') . ")";
        return [$start, $end, $label];
    }

    private function computeTopItems($orders, int $limit = 10): array
    {
        $counts = [];

        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $name = (string) ($item->name ?? 'Unknown Item');
                $qty  = (int) ($item->qty ?? 0);

                if (!isset($counts[$name])) $counts[$name] = 0;
                $counts[$name] += $qty;
            }
        }

        arsort($counts);

        $top = [];
        foreach ($counts as $name => $qty) {
            $top[] = ['name' => $name, 'qty' => $qty];
            if (count($top) >= $limit) break;
        }

        return $top;
    }
}