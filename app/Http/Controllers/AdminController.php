<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Admin dashboard page
    public function index()
    {
        $statusCol = $this->detectStatusColumn() ?? 'status';

        $orders = Order::with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $activeCount = Order::where('payment_status', 'paid')
            ->whereIn($statusCol, ['preparing', 'serving'])
            ->count();

        $pendingCount = Order::where('payment_status', 'paid')
            ->where($statusCol, 'pending')
            ->count();

        $cancelledCount = Order::where('payment_status', 'paid')
            ->where($statusCol, 'cancelled')
            ->count();

        $servedCount = Order::where('payment_status', 'paid')
            ->where($statusCol, 'served')
            ->count();

        return view('admin.dashboard', compact(
            'orders',
            'activeCount',
            'pendingCount',
            'cancelledCount',
            'servedCount'
        ));
    }


    public function analytics()
{
    return view('admin.dashboard-analytics');
}


    /**
     * SALES REPORT PAGE
     */
    public function dailySalesReport(Request $request)
    {
        $period = $request->query('period', 'daily');
        $selectedDate = $request->query('date');

        $fromDate = $request->query('from');
        $toDate = $request->query('to');

        [$start, $end, $rangeLabel, $filterMode, $selectedDateValue, $fromValue, $toValue] =
            $this->resolveSalesDateRange($period, $selectedDate, $fromDate, $toDate);

        $paidOrders = Order::with('items')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalSales = (float) $paidOrders->sum('total');
        $paidCount  = (int) $paidOrders->count();
        $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

        [$chartLabels, $chartValues] = $this->buildSalesSeries($paidOrders, $period, $start, $end, $filterMode);
        $topItems = $this->computeTopItems($paidOrders, 5);

        return view('admin.daily-sales-report', [
            'period'       => $period,
            'selectedDate' => $selectedDateValue,
            'from'         => $fromValue,
            'to'           => $toValue,
            'filterMode'   => $filterMode,
            'rangeLabel'   => $rangeLabel,

            'paidOrders'   => $paidOrders,
            'totalSales'   => $totalSales,
            'paidCount'    => $paidCount,
            'avgOrder'     => $avgOrder,

            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues,
            'topItems'     => $topItems,
        ]);
    }

    /**
     * PRINT VIEW
     */
    public function printSalesReport(Request $request)
    {
        $period = $request->query('period', 'daily');
        $selectedDate = $request->query('date');

        $fromDate = $request->query('from');
        $toDate = $request->query('to');

        [$start, $end, $rangeLabel, $filterMode, $selectedDateValue, $fromValue, $toValue] =
            $this->resolveSalesDateRange($period, $selectedDate, $fromDate, $toDate);

        $paidOrders = Order::with('items')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalSales = (float) $paidOrders->sum('total');
        $paidCount  = (int) $paidOrders->count();
        $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

        [$chartLabels, $chartValues] = $this->buildSalesSeries($paidOrders, $period, $start, $end, $filterMode);
        $topItems = $this->computeTopItems($paidOrders, 10);

        return view('admin.reports.sales-report-print', [
            'period'       => $period,
            'selectedDate' => $selectedDateValue,
            'from'         => $fromValue,
            'to'           => $toValue,
            'filterMode'   => $filterMode,
            'rangeLabel'   => $rangeLabel,
            'generatedAt'  => Carbon::now()->format('Y-m-d h:i A'),

            'paidOrders'   => $paidOrders,
            'totalSales'   => $totalSales,
            'paidCount'    => $paidCount,
            'avgOrder'     => $avgOrder,

            'chartLabels'  => $chartLabels,
            'chartValues'  => $chartValues,
            'topItems'     => $topItems,
        ]);
    }

    /**
     * CSV DOWNLOAD
     */
    public function exportSalesReportCsv(Request $request)
    {
        $period = $request->query('period', 'daily');
        $selectedDate = $request->query('date');

        $fromDate = $request->query('from');
        $toDate = $request->query('to');

        [$start, $end, $rangeLabel, $filterMode] =
            $this->resolveSalesDateRange($period, $selectedDate, $fromDate, $toDate);

        $paidOrders = Order::with('items')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalSales = (float) $paidOrders->sum('total');
        $paidCount  = (int) $paidOrders->count();
        $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

        [$chartLabels, $chartValues] = $this->buildSalesSeries($paidOrders, $period, $start, $end, $filterMode);
        $topItems = $this->computeTopItems($paidOrders, 10);

        $filename = "sales_report_" . $start->format('Ymd') . "_to_" . $end->format('Ymd') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use (
            $period, $rangeLabel, $start, $end,
            $totalSales, $paidCount, $avgOrder,
            $paidOrders, $chartLabels, $chartValues, $topItems
        ) {
            $out = fopen('php://output', 'w');

            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($out, ['99 SILOG CAFE']);
            fputcsv($out, ['SALES REPORT']);
            fputcsv($out, ['']);
            fputcsv($out, ['Period', ucfirst($period)]);
            fputcsv($out, ['Range', $rangeLabel]);
            fputcsv($out, ['Start', $start->format('Y-m-d H:i:s')]);
            fputcsv($out, ['End', $end->format('Y-m-d H:i:s')]);
            fputcsv($out, ['Generated', now()->format('Y-m-d h:i A')]);
            fputcsv($out, ['']);
            fputcsv($out, ['Total Sales (Paid)', '₱' . number_format($totalSales, 2)]);
            fputcsv($out, ['Paid Orders', $paidCount]);
            fputcsv($out, ['Average Order', '₱' . number_format($avgOrder, 2)]);
            fputcsv($out, ['']);
            fputcsv($out, ['NOTE', 'This report includes only PAID orders (Xendit successful payments).']);
            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

            fputcsv($out, ['SALES BREAKDOWN']);
            fputcsv($out, ['Bucket', 'Total Sales']);
            for ($i = 0; $i < count($chartLabels); $i++) {
                fputcsv($out, [
                    (string) ($chartLabels[$i] ?? ''),
                    '₱' . number_format((float) ($chartValues[$i] ?? 0), 2)
                ]);
            }
            fputcsv($out, ['']);
            fputcsv($out, ['============================================================']);
            fputcsv($out, ['']);

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

            fputcsv($out, ['PAID ORDERS LIST']);
            fputcsv($out, ['Order Code', 'Table Number', 'Total', 'Status', 'Paid Time']);
            foreach ($paidOrders as $order) {
                fputcsv($out, [
                    (string) $order->order_code,
                    (string) $order->table_number,
                    '₱' . number_format((float)$order->total, 2),
                    (string) ($order->status ?? ''),
                    optional($order->created_at)->format('Y-m-d h:i A'),
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * XLS DOWNLOAD
     */
    public function exportSalesReportXls(Request $request)
    {
        $period = $request->query('period', 'daily');
        $selectedDate = $request->query('date');

        $fromDate = $request->query('from');
        $toDate = $request->query('to');

        [$start, $end, $rangeLabel, $filterMode, $selectedDateValue, $fromValue, $toValue] =
            $this->resolveSalesDateRange($period, $selectedDate, $fromDate, $toDate);

        $paidOrders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalSales = (float) $paidOrders->sum('total');
        $paidCount  = (int) $paidOrders->count();
        $avgOrder   = $paidCount > 0 ? ($totalSales / $paidCount) : 0;

        $filename = "sales_report_" . $start->format('Ymd') . "_to_" . $end->format('Ymd') . ".xls";

        $html = view('admin.reports.sales-report-excel', [
            'period'       => $period,
            'selectedDate' => $selectedDateValue,
            'from'         => $fromValue,
            'to'           => $toValue,
            'filterMode'   => $filterMode,
            'rangeLabel'   => $rangeLabel,
            'start'        => $start,
            'end'          => $end,
            'generatedAt'  => Carbon::now()->format('Y-m-d h:i A'),

            'paidOrders'   => $paidOrders,
            'totalSales'   => $totalSales,
            'paidCount'    => $paidCount,
            'avgOrder'     => $avgOrder,
        ])->render();

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Resolve period/date or custom from/to
     */
    private function resolveSalesDateRange(string $period, ?string $selectedDate, ?string $fromDate, ?string $toDate): array
    {
        $hasFromTo = !empty($fromDate) && !empty($toDate);

        if ($hasFromTo) {
            $from = Carbon::parse($fromDate)->startOfDay();
            $to = Carbon::parse($toDate)->endOfDay();

            if ($from->gt($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }

            $rangeLabel = 'Custom Range (' . $from->format('M d, Y') . ' - ' . $to->format('M d, Y') . ')';

            return [
                $from,
                $to,
                $rangeLabel,
                'range',
                $selectedDate ?: Carbon::today()->toDateString(),
                $from->toDateString(),
                $to->toDateString(),
            ];
        }

        $day = $selectedDate ? Carbon::parse($selectedDate) : Carbon::today();
        [$start, $end, $rangeLabel] = $this->getRangeByPeriod($period, $day);

        return [
            $start,
            $end,
            $rangeLabel,
            'period',
            $day->toDateString(),
            $fromDate,
            $toDate,
        ];
    }

    /**
     * Helper: compute date range by period
     */
    private function getRangeByPeriod(string $period, Carbon $day): array
    {
        $period = strtolower($period);

        if ($period === 'weekly') {
            $start = $day->copy()->startOfDay();
            $end   = $day->copy()->addDays(6)->endOfDay();
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

    /**
     * Build chart series
     */
    private function buildSalesSeries($orders, string $period, Carbon $start, Carbon $end, string $filterMode = 'period'): array
    {
        if ($filterMode === 'range') {
            $labels = [];
            $values = [];
            $map = [];

            $cursor = $start->copy()->startOfDay();
            $last = $end->copy()->startOfDay();

            while ($cursor->lte($last)) {
                $key = $cursor->toDateString();
                $map[$key] = 0.0;
                $labels[] = $cursor->format('M d');
                $cursor->addDay();
            }

            foreach ($orders as $o) {
                $key = optional($o->created_at)->toDateString();
                if (isset($map[$key])) {
                    $map[$key] += (float) $o->total;
                }
            }

            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($last)) {
                $values[] = (float) $map[$cursor->toDateString()];
                $cursor->addDay();
            }

            return [$labels, $values];
        }

        $period = strtolower($period);

        $labels = [];
        $values = [];

        if ($period === 'daily') {
            for ($h = 10; $h <= 22; $h++) {
                $labels[] = Carbon::createFromTime($h, 0)->format('g A');
                $values[] = 0.0;
            }

            foreach ($orders as $o) {
                $hour = (int) optional($o->created_at)->format('G');

                if ($hour >= 10 && $hour <= 22) {
                    $values[$hour - 10] += (float) $o->total;
                }
            }

            return [$labels, $values];
        }

        if ($period === 'weekly') {
            $cursor = $start->copy()->startOfDay();
            $map = [];

            while ($cursor->lte($end)) {
                $key = $cursor->toDateString();
                $map[$key] = 0.0;
                $labels[] = $cursor->format('D • M d');
                $cursor->addDay();
            }

            foreach ($orders as $o) {
                $key = optional($o->created_at)->toDateString();
                if (isset($map[$key])) {
                    $map[$key] += (float) $o->total;
                }
            }

            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($end)) {
                $values[] = (float) $map[$cursor->toDateString()];
                $cursor->addDay();
            }

            return [$labels, $values];
        }

        if ($period === 'monthly') {
            $cursor = $start->copy()->startOfDay();
            $map = [];

            while ($cursor->lte($end)) {
                $key = $cursor->toDateString();
                $map[$key] = 0.0;
                $labels[] = $cursor->format('M d');
                $cursor->addDay();
            }

            foreach ($orders as $o) {
                $key = optional($o->created_at)->toDateString();
                if (isset($map[$key])) {
                    $map[$key] += (float) $o->total;
                }
            }

            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($end)) {
                $values[] = (float) $map[$cursor->toDateString()];
                $cursor->addDay();
            }

            return [$labels, $values];
        }

        if ($period === 'yearly') {
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::createFromDate($start->year, $m, 1)->format('M');
                $values[] = 0.0;
            }

            foreach ($orders as $o) {
                $month = (int) optional($o->created_at)->format('n');
                $values[$month - 1] += (float) $o->total;
            }

            return [$labels, $values];
        }

        $cursor = $start->copy();
        $map = [];
        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $map[$key] = 0.0;
            $labels[] = $cursor->format('M d');
            $cursor->addDay();
        }

        foreach ($orders as $o) {
            $key = optional($o->created_at)->toDateString();
            if (isset($map[$key])) {
                $map[$key] += (float) $o->total;
            }
        }

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $values[] = (float) $map[$cursor->toDateString()];
            $cursor->addDay();
        }

        return [$labels, $values];
    }

    /**
     * Best sellers from Order->items safely
     */
    private function computeTopItems($orders, int $limit = 5): array
    {
        $counts = [];

        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $name = (string) ($item->name ?? 'Unknown Item');
                $qty  = (int) ($item->qty ?? 0);

                if (!isset($counts[$name])) {
                    $counts[$name] = 0;
                }
                $counts[$name] += $qty;
            }
        }

        arsort($counts);

        $top = [];
        foreach ($counts as $name => $qty) {
            $top[] = ['name' => $name, 'qty' => $qty];
            if (count($top) >= $limit) {
                break;
            }
        }

        return $top;
    }

    private function detectStatusColumn(): ?string
    {
        $candidates = ['status', 'order_status', 'tracking_status'];

        foreach ($candidates as $col) {
            if (Schema::hasColumn('orders', $col)) {
                return $col;
            }
        }

        return null;
    }

    public function tableManagement()
    {
        $tables = Table::orderBy('number')->get()->keyBy('number');

        $statusCol = $this->detectStatusColumn() ?? 'status';

        $orders = Order::orderBy('created_at', 'asc')->get();

        foreach ($orders as $o) {
            $raw = $o->{$statusCol} ?? $o->status ?? '';
            $o->status = strtolower(trim((string) $raw));
        }

        $activeCount    = $orders->whereIn('status', ['preparing', 'serving'])->count();
        $pendingCount   = $orders->where('status', 'pending')->count();
        $cancelledCount = $orders->where('status', 'cancelled')->count();
        $servedCount    = $orders->where('status', 'served')->count();

        return view('admin.table-management', compact(
            'tables',
            'activeCount',
            'pendingCount',
            'cancelledCount',
            'servedCount'
        ));
    }

    public function toggleTable(Request $request, $number)
    {
        $n = (int) $number;

        if ($n < 1 || $n > 10) {
            return response()->json(['error' => 'Invalid table number'], 422);
        }

        $table = Table::where('number', $n)->firstOrFail();
        $table->is_available = !$table->is_available;
        $table->save();

        return response()->json([
            'number'       => $table->number,
            'is_available' => $table->is_available,
            'status_text'  => $table->is_available ? 'Available' : 'Unavailable',
        ]);
    }

    public function storeOrdersData(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.customer_name' => 'required|string',
            'orders.*.items' => 'required|array',
            'orders.*.total' => 'required|numeric',
        ]);

        \Log::info('Received orders data:', $validated['orders']);

        return response()->json(['message' => 'Orders data received successfully', 'status' => true]);
    }
}