<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard Analytics</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="shortcut icon" href="{{ asset('images/Logogogo.png') }}">
  <style>
    :root{
      --bg:#fffdf8;
      --panel:#ffffff;
      --sidebar:#e4e3e3;
      --text:#1f2937;
      --muted:#6b7280;
      --orange:#f59e0b;
      --orange-dark:#d97706;
      --border:#ece7df;
      --shadow-soft:0 12px 30px rgba(15, 23, 42, .08);
      --shadow-light:0 8px 18px rgba(15, 23, 42, .05);
      --radius:18px;
    }

    *{ box-sizing:border-box; }
    html{ scroll-behavior:smooth; }

    body{
      margin:0;
      font-family:'Figtree', sans-serif;
      background:
        radial-gradient(circle at top right, rgba(245,158,11,.08), transparent 22%),
        linear-gradient(180deg, #fffefb 0%, #fffaf0 100%);
      color:var(--text);
    }

     .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:240px 1fr;
    }

   /* Sidebar */
    .sidebar{
      background:var(--sidebar);
      padding:18px 14px;
      border-right:1px solid rgba(0,0,0,.06);
        position:sticky;
      top:0;
      height:100vh;
    }
    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding:6px 6px 14px;
    }
    .logo-box{
      width:120px;
      height:58px;
      background:#fff;
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{
      width:100%;
      height:100%;
      object-fit:contain;
      padding:6px;
    }

    .side-section-title{
      font-size:11px;
      font-weight:800;
      color:#1f2937;
      margin:14px 6px 8px;
      text-transform:uppercase;
      opacity:.85;
    }
    .nav{
      display:flex;
      flex-direction:column;
      gap:8px;
      padding:0 6px;
    }
    .nav a{
      text-decoration:none;
      font-size:13px;
      padding:10px 12px;
      border-radius:999px;
      color:#111;
      display:flex;
      align-items:center;
      gap:8px;
      transition:.15s ease;
      background:rgba(255,255,255,.55);
      border:1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background:rgba(255,184,30,.25); }
    .nav a.active{
      background:var(--orange);
      color:#111;
      font-weight:800;
      border-color:rgba(0,0,0,.06);
      box-shadow:0 6px 14px rgba(0,0,0,.12);
    }
    .content{
      padding:24px;
      background:transparent;
    }

    .header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      margin-bottom:18px;
    }

    .header-left .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:7px 12px;
      border-radius:999px;
      background:rgba(245,158,11,.10);
      color:#a16207;
      border:1px solid rgba(245,158,11,.16);
      font-size:12px;
      font-weight:900;
      margin-bottom:10px;
    }

    .header-left .title{
      font-size:30px;
      font-weight:900;
      color:var(--orange);
      letter-spacing:.2px;
      margin:0;
      line-height:1.05;
    }

    .header-left .subtitle{
      margin-top:8px;
      font-size:13px;
      color:var(--muted);
      font-weight:700;
      max-width:760px;
    }

    .logout-btn{
      padding:11px 16px;
      background:linear-gradient(135deg, #fbbf24, #f59e0b);
      border:none;
      border-radius:14px;
      cursor:pointer;
      font-weight:900;
      box-shadow:0 10px 24px rgba(245,158,11,.20);
      color:#111827;
    }

    .summary-grid{
      display:grid;
      grid-template-columns:1.55fr 1fr;
      gap:16px;
      margin-bottom:18px;
    }

    .hero-card,
    .quick-card,
    .section-card{
      background:rgba(255,255,255,.92);
      border:1px solid rgba(0,0,0,.05);
      border-radius:var(--radius);
      box-shadow:var(--shadow-soft);
      backdrop-filter:blur(8px);
    }

    .hero-card{
      padding:18px;
      background:
        radial-gradient(circle at top right, rgba(252,211,77,.22), transparent 24%),
        linear-gradient(135deg, rgba(255,244,219,.95), rgba(255,255,255,.96) 55%);
    }

    .hero-title{
      font-size:20px;
      font-weight:900;
      margin:0 0 6px;
      color:#111827;
    }

    .hero-sub{
      color:var(--muted);
      font-size:13px;
      font-weight:700;
      margin-bottom:14px;
    }

    .hero-metrics{
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:12px;
    }

    .hero-metric{
      background:rgba(255,255,255,.88);
      border:1px solid rgba(245,158,11,.16);
      border-radius:16px;
      padding:13px;
      box-shadow:0 6px 16px rgba(0,0,0,.04);
    }

    .hero-metric .label{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
      margin-bottom:7px;
    }

    .hero-metric .value{
      font-size:24px;
      font-weight:900;
      color:#111827;
      line-height:1.05;
    }

    .hero-metric .small{
      margin-top:7px;
      font-size:11.5px;
      color:var(--muted);
      font-weight:700;
    }

    .quick-card{
      padding:16px;
      display:flex;
      flex-direction:column;
      gap:10px;
      background:
        radial-gradient(circle at top right, rgba(245,158,11,.10), transparent 20%),
        #fff;
    }

    .card-title{
      font-size:15px;
      font-weight:900;
      margin:0;
      color:#111827;
    }

    .card-sub{
      font-size:12px;
      color:var(--muted);
      font-weight:700;
      margin:0;
    }

    .quick-links{
      display:grid;
      gap:10px;
    }

    .quick-link{
      display:block;
      text-decoration:none;
      background:linear-gradient(180deg, #fffdfa 0%, #fff 100%);
      border:1px solid var(--border);
      border-radius:15px;
      padding:13px;
      color:#111827;
      transition:.18s ease;
      box-shadow:0 6px 14px rgba(0,0,0,.03);
    }

    .quick-link:hover{
      border-color:rgba(245,158,11,.30);
      background:#fff7e6;
      transform:translateY(-1px);
    }

    .quick-link strong{
      display:block;
      font-size:13px;
      margin-bottom:4px;
    }

    .quick-link span{
      font-size:12px;
      color:var(--muted);
      font-weight:700;
    }

    .target-wrap{
      margin:0 0 18px;
      padding:18px;
      border-radius:24px;
      background:
        radial-gradient(circle at top right, rgba(245,158,11,.10), transparent 22%),
        linear-gradient(135deg, rgba(255,245,224,.98), rgba(255,255,255,.98));
      border:1px solid rgba(245,158,11,.14);
      box-shadow:var(--shadow-soft);
    }

    .target-top{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      flex-wrap:wrap;
      margin-bottom:14px;
    }

    .target-title{
      margin:0;
      font-size:22px;
      font-weight:900;
      color:#111827;
    }

    .target-sub{
      margin:5px 0 0;
      font-size:13px;
      color:var(--muted);
      font-weight:700;
    }

    .target-badge{
      padding:8px 14px;
      border-radius:999px;
      background:#fff;
      border:1px solid rgba(245,158,11,.18);
      color:#9a6700;
      font-size:12px;
      font-weight:900;
      box-shadow:0 6px 14px rgba(0,0,0,.04);
    }

    .target-grid{
      display:grid;
      grid-template-columns:repeat(4, minmax(0,1fr));
      gap:14px;
      margin-bottom:14px;
    }

    .target-card{
      background:rgba(255,255,255,.92);
      border:1px solid rgba(245,158,11,.14);
      border-radius:18px;
      padding:14px;
      box-shadow:0 8px 20px rgba(0,0,0,.04);
    }

    .target-card .label{
      font-size:12px;
      color:var(--muted);
      font-weight:800;
      margin-bottom:8px;
    }

    .target-card .value{
      font-size:24px;
      font-weight:900;
      color:#111827;
      line-height:1.05;
    }

    .target-card .hint{
      margin-top:8px;
      font-size:11.5px;
      color:var(--muted);
      font-weight:700;
    }

    .target-progress-card{
      background:#fff;
      border:1px solid var(--border);
      border-radius:18px;
      padding:16px;
      box-shadow:0 8px 20px rgba(0,0,0,.04);
    }

    .target-progress-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin-bottom:10px;
      flex-wrap:wrap;
    }

    .target-progress-head strong{
      font-size:14px;
      color:#111827;
    }

    .target-progress-head span{
      font-size:12px;
      font-weight:900;
      color:#9a6700;
      background:rgba(245,158,11,.10);
      border:1px solid rgba(245,158,11,.18);
      padding:5px 10px;
      border-radius:999px;
    }

    .progress-track{
      width:100%;
      height:14px;
      border-radius:999px;
      background:#f3efe7;
      overflow:hidden;
      border:1px solid rgba(0,0,0,.04);
    }

    .progress-bar{
      height:100%;
      border-radius:999px;
      background:linear-gradient(90deg, #fbbf24, #f59e0b);
      transition:width .35s ease;
    }

    .progress-foot{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      margin-top:10px;
      font-size:12px;
      color:var(--muted);
      font-weight:700;
    }

    .analytics-wrap{
      margin:0 0 18px;
      padding:20px;
      border-radius:28px;
      background:
        radial-gradient(circle at top right, rgba(245,158,11,.10), transparent 20%),
        linear-gradient(135deg, #fff8eb 0%, #fffdf8 55%, #ffffff 100%);
      border:1px solid rgba(245,158,11,.12);
      box-shadow:0 14px 34px rgba(15, 23, 42, .07);
    }

    .analytics-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      flex-wrap:wrap;
      margin-bottom:16px;
    }

    .analytics-title{
      margin:0;
      font-size:24px;
      font-weight:900;
      color:#111827;
    }

    .analytics-subtitle{
      margin:5px 0 0;
      font-size:13px;
      color:#6b7280;
      font-weight:700;
    }

    .analytics-badge{
      padding:8px 14px;
      border-radius:999px;
      background:#fff;
      border:1px solid rgba(245,158,11,.16);
      color:#9a6700;
      font-size:12px;
      font-weight:900;
      box-shadow:0 6px 14px rgba(0,0,0,.04);
    }

    .analytics-grid{
      display:grid;
      grid-template-columns:1.45fr .9fr;
      gap:18px;
      align-items:start;
    }

    .chart-card{
      background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,252,246,.98));
      border:1px solid rgba(240,225,200,.9);
      border-radius:26px;
      padding:18px 18px 18px;
      box-shadow:
        0 14px 26px rgba(15,23,42,.05),
        inset 0 1px 0 rgba(255,255,255,.85);
      position:relative;
      overflow:hidden;
    }

    .chart-card::before{
      content:"";
      position:absolute;
      top:0;
      left:0;
      width:100%;
      height:5px;
      background:linear-gradient(90deg, #f59e0b, #fcd34d);
    }

    .chart-card.sales-card{
      min-height:auto;
    }

    .chart-card.inventory-card{
      min-height:auto;
      display:flex;
      flex-direction:column;
    }

    .chart-card-head{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
      margin-bottom:10px;
      flex-wrap:wrap;
    }

    .chart-card h3{
      margin:0;
      font-size:19px;
      font-weight:900;
      color:#111827;
    }

    .chart-card p{
      margin:6px 0 0;
      font-size:12.5px;
      color:#6b7280;
      font-weight:700;
    }

    .chart-chip{
      white-space:nowrap;
      padding:7px 12px;
      border-radius:999px;
      background:linear-gradient(180deg, #fff6df, #fff1cc);
      border:1px solid rgba(245,158,11,.18);
      color:#9a6700;
      font-size:11px;
      font-weight:900;
    }

    .chart-note{
      margin-top:4px;
      margin-bottom:10px;
      font-size:12px;
      color:#8b95a7;
      font-weight:700;
    }

    .sales-toolbar{
      display:flex;
      gap:10px;
      align-items:end;
      flex-wrap:wrap;
      margin-bottom:10px;
    }

    .sales-toolbar .field{
      display:flex;
      flex-direction:column;
      gap:6px;
    }

    .sales-toolbar label{
      font-size:11px;
      font-weight:800;
      color:#6b7280;
    }

    .sales-toolbar select,
    .sales-toolbar input[type="date"]{
      height:40px;
      padding:0 12px;
      border-radius:12px;
      border:1px solid #e7dcc6;
      background:#fffdfa;
      color:#374151;
      font-size:12px;
      font-weight:700;
      outline:none;
    }

    .sales-toolbar button{
      height:40px;
      padding:0 16px;
      border:none;
      border-radius:12px;
      background:linear-gradient(135deg, #fbbf24, #f59e0b);
      color:#111827;
      font-size:12px;
      font-weight:900;
      cursor:pointer;
      box-shadow:0 8px 18px rgba(245,158,11,.18);
    }

    .sales-toolbar a{
      height:40px;
      padding:0 14px;
      border-radius:12px;
      border:1px solid #e7dcc6;
      background:#fff;
      color:#6b7280;
      font-size:12px;
      font-weight:900;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      justify-content:center;
    }

    .sales-card canvas{
      width:100% !important;
      height:260px !important;
      max-height:260px !important;
    }

    .inventory-card canvas{
      width:100% !important;
      height:220px !important;
      max-height:220px !important;
    }

    .inventory-legend{
      display:grid;
      grid-template-columns:1fr;
      gap:10px;
      margin-top:10px;
    }

    .inventory-legend-item{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      background:#fffdfa;
      border:1px solid #efe7d8;
      border-radius:14px;
      padding:10px 12px;
    }

    .inventory-legend-left{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
    }

    .inventory-dot{
      width:12px;
      height:12px;
      border-radius:999px;
      flex:0 0 12px;
    }

    .inventory-name{
      font-size:12.5px;
      font-weight:800;
      color:#374151;
    }

    .inventory-value{
      font-size:13px;
      font-weight:900;
      color:#111827;
    }

    .dashboard-sections{
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:16px;
      margin-bottom:18px;
    }

    .section-card{
      padding:15px;
      background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,250,240,.96));
    }

    .section-top{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin-bottom:12px;
    }

    .section-tag{
      font-size:11px;
      font-weight:900;
      padding:5px 9px;
      border-radius:999px;
      background:rgba(245,158,11,.10);
      border:1px solid rgba(245,158,11,.16);
      color:#9a6700;
    }

    .mini-stats{
      display:grid;
      grid-template-columns:repeat(2, minmax(0,1fr));
      gap:10px;
    }

    .mini-stat{
      background:#fff;
      border:1px solid var(--border);
      border-radius:15px;
      padding:12px;
      box-shadow:0 6px 14px rgba(0,0,0,.03);
    }

    .mini-stat .label{
      font-size:11px;
      color:var(--muted);
      font-weight:800;
      margin-bottom:6px;
    }

    .mini-stat .value{
      font-size:18px;
      font-weight:900;
      color:#111827;
      line-height:1;
    }

    .mini-stat .hint{
      margin-top:6px;
      font-size:11px;
      color:var(--muted);
      font-weight:700;
    }

    .list-box{
      margin-top:12px;
      background:#fffdfa;
      border:1px solid var(--border);
      border-radius:15px;
      padding:12px;
    }

    .list-box-title{
      font-size:12px;
      font-weight:900;
      margin-bottom:8px;
      color:#111827;
    }

    .simple-list{
      margin:0;
      padding-left:18px;
      font-size:12.5px;
    }

    .simple-list li{
      margin-top:6px;
      color:#374151;
      font-weight:700;
    }

    @media (max-width: 1200px){
      .summary-grid,
      .dashboard-sections,
      .analytics-grid{
        grid-template-columns:1fr;
      }

      .hero-metrics,
      .target-grid{
        grid-template-columns:1fr 1fr;
      }

      .chart-card.sales-card,
      .chart-card.inventory-card{
        min-height:auto;
      }
    }

    @media (max-width: 720px){
      .shell{ grid-template-columns:1fr; }
      .sidebar{ display:none; }
      .content{ padding:16px; }
      .target-grid,
      .mini-stats,
      .hero-metrics{ grid-template-columns:1fr; }
      .dashboard-sections,
      .summary-grid{ grid-template-columns:1fr; }
      .analytics-wrap,
      .target-wrap{ padding:14px; border-radius:18px; }
      .chart-card{ padding:14px; border-radius:20px; }
      .sales-card canvas{
        height:220px !important;
        max-height:220px !important;
      }
      .inventory-card canvas{
        height:200px !important;
        max-height:200px !important;
      }
      .header{ flex-direction:column; align-items:stretch; }
    }
  </style>
</head>

<body>
  @php
    use Carbon\Carbon;
    use App\Models\Order;
    use App\Models\Ingredient;

    $userPosition = strtolower(auth()->user()->position ?? '');
    $isAdmin = $userPosition === 'admin';
    $isCashier = $userPosition === 'cashier';

    $today = now();

    $paidOrdersAll = Order::with('items')
      ->where('payment_status', 'paid')
      ->orderBy('created_at', 'asc')
      ->get();

    $todayPaidOrders = $paidOrdersAll->filter(fn($o) => optional($o->created_at)->isToday());
    $monthPaidOrders = $paidOrdersAll->filter(fn($o) => optional($o->created_at)->format('Y-m') === $today->format('Y-m'));

    $todaySales = (float) $todayPaidOrders->sum('total');
    $monthSales = (float) $monthPaidOrders->sum('total');
    $todayOrdersCount = $todayPaidOrders->count();
    $avgTodaySale = $todayOrdersCount > 0 ? $todaySales / $todayOrdersCount : 0;

    $expectedSalesToday = 5000;
    $remainingSalesToday = max($expectedSalesToday - $todaySales, 0);
    $salesProgressToday = $expectedSalesToday > 0
      ? min(($todaySales / $expectedSalesToday) * 100, 100)
      : 0;
    $excessSalesToday = max($todaySales - $expectedSalesToday, 0);

    $activeOrders = $paidOrdersAll->filter(function($o){
      $s = strtolower(trim((string)($o->status ?? '')));
      return $s === '' || ($s === 'preparing' && empty($o->eta_minutes));
    });

    $preparingOrders = $paidOrdersAll->filter(function($o){
      $s = strtolower(trim((string)($o->status ?? '')));
      return $s === 'preparing' && !empty($o->eta_minutes);
    });

    $servingOrders = $paidOrdersAll->filter(fn($o) => strtolower(trim((string)($o->status ?? ''))) === 'serving');
    $servedOrders = $paidOrdersAll->filter(fn($o) => strtolower(trim((string)($o->status ?? ''))) === 'served');

    $inventoryTotal = Ingredient::count();
    $inventoryLow = Ingredient::where('stock_qty', '>', 0)
      ->whereColumn('stock_qty', '<=', 'reorder_level')
      ->count();
    $inventoryOut = Ingredient::where('stock_qty', '<=', 0)->count();
    $inventoryOver = Ingredient::where('overstock_level', '>', 0)
      ->whereColumn('stock_qty', '>', 'overstock_level')
      ->count();

    $lowStockItems = Ingredient::where(function ($q) {
        $q->where('stock_qty', '<=', 0)
          ->orWhereColumn('stock_qty', '<=', 'reorder_level');
      })
      ->orderBy('stock_qty', 'asc')
      ->limit(5)
      ->get();

    $topSelling = [];
    foreach ($paidOrdersAll as $order) {
      foreach ($order->items as $item) {
        $name = trim((string) ($item->name ?? 'Unknown Item'));
        $qty = (int) ($item->qty ?? 0);
        if (!isset($topSelling[$name])) {
          $topSelling[$name] = 0;
        }
        $topSelling[$name] += $qty;
      }
    }
    arsort($topSelling);
    $topSelling = array_slice($topSelling, 0, 5, true);

    $range = request('range', '7days');
    $allowedRanges = ['7days', '30days', 'month', 'all', 'custom'];
    if (!in_array($range, $allowedRanges, true)) {
      $range = '7days';
    }

    $customFrom = request('from');
    $customTo = request('to');

    $filterLabel = 'Last 7 Days';
    $salesLabels = [];
    $salesData = [];
    $expectedLineData = [];

    if ($range === '7days') {
      $filterLabel = 'Last 7 Days';
      for ($i = 6; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $salesLabels[] = $day->format('M d');

        $dayTotal = Order::where('payment_status', 'paid')
          ->whereDate('created_at', $day->toDateString())
          ->sum('total');

        $salesData[] = (float) $dayTotal;
        $expectedLineData[] = (float) $expectedSalesToday;
      }
    } elseif ($range === '30days') {
      $filterLabel = 'Last 30 Days';
      for ($i = 29; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $salesLabels[] = $day->format('M d');

        $dayTotal = Order::where('payment_status', 'paid')
          ->whereDate('created_at', $day->toDateString())
          ->sum('total');

        $salesData[] = (float) $dayTotal;
        $expectedLineData[] = (float) $expectedSalesToday;
      }
    } elseif ($range === 'month') {
      $filterLabel = 'This Month';
      $startOfMonth = now()->startOfMonth();
      $endOfMonth = now()->copy();

      for ($day = $startOfMonth->copy(); $day->lte($endOfMonth); $day->addDay()) {
        $salesLabels[] = $day->format('M d');

        $dayTotal = Order::where('payment_status', 'paid')
          ->whereDate('created_at', $day->toDateString())
          ->sum('total');

        $salesData[] = (float) $dayTotal;
        $expectedLineData[] = (float) $expectedSalesToday;
      }
    } elseif ($range === 'all') {
      $filterLabel = 'All Time';
      $firstOrder = Order::where('payment_status', 'paid')->orderBy('created_at', 'asc')->first();

      if ($firstOrder) {
        $startMonth = Carbon::parse($firstOrder->created_at)->startOfMonth();
        $endMonth = now()->startOfMonth();

        for ($month = $startMonth->copy(); $month->lte($endMonth); $month->addMonth()) {
          $salesLabels[] = $month->format('M Y');

          $monthTotal = Order::where('payment_status', 'paid')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->sum('total');

          $salesData[] = (float) $monthTotal;
          $expectedLineData[] = (float) ($expectedSalesToday * $month->daysInMonth);
        }
      }
    } elseif ($range === 'custom') {
      $fromDate = $customFrom ? Carbon::parse($customFrom)->startOfDay() : now()->subDays(6)->startOfDay();
      $toDate = $customTo ? Carbon::parse($customTo)->endOfDay() : now()->endOfDay();

      if ($fromDate->gt($toDate)) {
        [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
      }

      $daysDiff = $fromDate->copy()->startOfDay()->diffInDays($toDate->copy()->startOfDay());
      $filterLabel = 'Custom Range';

      if ($daysDiff <= 31) {
        for ($day = $fromDate->copy()->startOfDay(); $day->lte($toDate->copy()->startOfDay()); $day->addDay()) {
          $salesLabels[] = $day->format('M d');

          $dayTotal = Order::where('payment_status', 'paid')
            ->whereDate('created_at', $day->toDateString())
            ->sum('total');

          $salesData[] = (float) $dayTotal;
          $expectedLineData[] = (float) $expectedSalesToday;
        }
      } else {
        $startMonth = $fromDate->copy()->startOfMonth();
        $endMonth = $toDate->copy()->startOfMonth();

        for ($month = $startMonth->copy(); $month->lte($endMonth); $month->addMonth()) {
          $salesLabels[] = $month->format('M Y');

          $monthTotal = Order::where('payment_status', 'paid')
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('total');

          $salesData[] = (float) $monthTotal;
          $expectedLineData[] = (float) ($expectedSalesToday * $month->daysInMonth);
        }
      }
    }

    $inventoryCounts = [
      'Healthy' => Ingredient::where('stock_qty', '>', 0)
        ->where(function ($q) {
          $q->whereNull('reorder_level')
            ->orWhereColumn('stock_qty', '>', 'reorder_level');
        })
        ->where(function ($q) {
          $q->where('overstock_level', '<=', 0)
            ->orWhereNull('overstock_level')
            ->orWhereColumn('stock_qty', '<=', 'overstock_level');
        })
        ->count(),

      'Low Stock' => Ingredient::where('stock_qty', '>', 0)
        ->whereColumn('stock_qty', '<=', 'reorder_level')
        ->count(),

      'Out of Stock' => Ingredient::where('stock_qty', '<=', 0)
        ->count(),

      'Overstock' => Ingredient::where('overstock_level', '>', 0)
        ->whereColumn('stock_qty', '>', 'overstock_level')
        ->count(),
    ];
  @endphp

  <div class="shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="logo-box">
          <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
        </div>
      </div>

      @if($isAdmin || $isCashier)
        <div class="side-section-title">Cashier Transaction</div>
        <nav class="nav">
               <a href="{{ route('admin.dashboard.analytics') }}" class="active">Dashboard</a>
          <a href="{{ route('admin.dashboard') }}">Order Management</a>
          <a href="{{ route('admin.table-management') }}">Table Management</a>
          <a href="{{ route('admin.daily-sales-report') }}">Sales Report</a>
        </nav>
      @endif

      @if($isAdmin)
        <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
        <nav class="nav">
          <a href="{{ route('admin.menu-management') }}">Menu Management</a>
          <a href="{{ route('admin.feedbacks') }}">Feedback Management</a>
          <a href="{{ route('admin.inventory') }}">Inventory Management</a>
          <a href="{{ route('admin.sales-stock-reports') }}">Stock Reports</a>
          <a href="{{ route('admin.users.index') }}">User Management</a>
        </nav>
      @endif
    </aside>

    <main class="content">
      <div class="header">
        <div class="header-left">
          <div class="eyebrow">Dashboard Analytics</div>
          <h1 class="title">Analytics, Sales & Inventory Overview</h1>
          <div class="subtitle">
            A separate analytics page so your current order management dashboard stays untouched.
          </div>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <section class="summary-grid">
        <div class="hero-card">
          <h2 class="hero-title">Business Overview</h2>
          <div class="hero-sub">Quick snapshot of today and this month.</div>

          <div class="hero-metrics">
            <div class="hero-metric">
              <div class="label">Today Sales</div>
              <div class="value">₱{{ number_format($todaySales, 2) }}</div>
              <div class="small">{{ $todayOrdersCount }} paid order{{ $todayOrdersCount === 1 ? '' : 's' }}</div>
            </div>

            <div class="hero-metric">
              <div class="label">Monthly Sales</div>
              <div class="value">₱{{ number_format($monthSales, 2) }}</div>
              <div class="small">{{ $monthPaidOrders->count() }} paid order{{ $monthPaidOrders->count() === 1 ? '' : 's' }}</div>
            </div>

            <div class="hero-metric">
              <div class="label">Average Sale Today</div>
              <div class="value">₱{{ number_format($avgTodaySale, 2) }}</div>
              <div class="small">Per paid order</div>
            </div>
          </div>
        </div>

        <div class="quick-card">
          <h3 class="card-title">Quick Access</h3>
          <p class="card-sub">Open the detailed pages already in your system.</p>

          <div class="quick-links">
            <a class="quick-link" href="{{ route('admin.dashboard') }}">
              <strong>Order Management</strong>
              <span>Go back to your current dashboard page.</span>
            </a>

            <a class="quick-link" href="{{ route('admin.table-management') }}">
              <strong>Table Management</strong>
              <span>Manage and update table statuses.</span>
            </a>

            <a class="quick-link" href="{{ route('admin.daily-sales-report') }}">
              <strong>Sales Report</strong>
              <span>Open the detailed sales report page.</span>
            </a>
          </div>
        </div>
      </section>

      <section class="target-wrap">
        <div class="target-top">
          <div>
            <h2 class="target-title">Actual vs Expected Sales</h2>
            <p class="target-sub">Today’s real earnings compared with your daily target</p>
          </div>
          <div class="target-badge">Daily Target Tracker</div>
        </div>

        <div class="target-grid">
          <div class="target-card">
            <div class="label">Actual Sales Today</div>
            <div class="value">₱{{ number_format($todaySales, 2) }}</div>
            <div class="hint">Real paid sales recorded today</div>
          </div>

          <div class="target-card">
            <div class="label">Expected Sales Today</div>
            <div class="value">₱{{ number_format($expectedSalesToday, 2) }}</div>
            <div class="hint">Current fixed daily target</div>
          </div>

          <div class="target-card">
            <div class="label">Remaining to Target</div>
            <div class="value">₱{{ number_format($remainingSalesToday, 2) }}</div>
            <div class="hint">Amount needed to reach target</div>
          </div>

          <div class="target-card">
            <div class="label">Above Target</div>
            <div class="value">₱{{ number_format($excessSalesToday, 2) }}</div>
            <div class="hint">Extra earnings beyond target</div>
          </div>
        </div>

        <div class="target-progress-card">
          <div class="target-progress-head">
            <strong>Target Progress</strong>
            <span>{{ number_format($salesProgressToday, 1) }}% reached</span>
          </div>

          <div class="progress-track">
            <div class="progress-bar" style="width: {{ number_format($salesProgressToday, 2, '.', '') }}%;"></div>
          </div>

          <div class="progress-foot">
            <div>Actual: ₱{{ number_format($todaySales, 2) }}</div>
            <div>Target: ₱{{ number_format($expectedSalesToday, 2) }}</div>
          </div>
        </div>
      </section>

      <section class="analytics-wrap">
        <div class="analytics-header">
          <div>
            <h2 class="analytics-title">Analytics Overview</h2>
            <p class="analytics-subtitle">A cleaner visual summary focused on sales and inventory</p>
          </div>
          <div class="analytics-badge">{{ $filterLabel }}</div>
        </div>

        <div class="analytics-grid">
          <div class="chart-card sales-card">
            <div class="chart-card-head">
              <div>
                <h3>Sales Trend</h3>
                <p>Actual sales versus expected sales based on your selected range</p>
              </div>
              <div class="chart-chip">Line Graph</div>
            </div>

            <form method="GET" action="{{ route('admin.dashboard.analytics') }}" class="sales-toolbar">
              <div class="field">
                <label for="range">Range</label>
                <select name="range" id="range" onchange="toggleCustomDates(this.value)">
                  <option value="7days" {{ $range === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                  <option value="30days" {{ $range === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                  <option value="month" {{ $range === 'month' ? 'selected' : '' }}>This Month</option>
                  <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All Time</option>
                  <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
              </div>

              <div class="field custom-date-field" style="{{ $range === 'custom' ? '' : 'display:none;' }}">
                <label for="from">From</label>
                <input type="date" id="from" name="from" value="{{ $customFrom }}">
              </div>

              <div class="field custom-date-field" style="{{ $range === 'custom' ? '' : 'display:none;' }}">
                <label for="to">To</label>
                <input type="date" id="to" name="to" value="{{ $customTo }}">
              </div>

              <button type="submit">Apply</button>
              <a href="{{ route('admin.dashboard.analytics') }}">Reset</a>
            </form>

            <div class="chart-note">
              The solid line shows actual paid sales, while the dashed line shows your expected target for the selected range.
            </div>

            <canvas id="salesChart"></canvas>
          </div>

          <div class="chart-card inventory-card">
            <div class="chart-card-head">
              <div>
                <h3>Inventory Summary</h3>
                <p>Current ingredient stock availability</p>
              </div>
              <div class="chart-chip">Doughnut</div>
            </div>

            <canvas id="inventoryChart"></canvas>

            <div class="inventory-legend">
              <div class="inventory-legend-item">
                <div class="inventory-legend-left">
                  <span class="inventory-dot" style="background:#22c55e;"></span>
                  <span class="inventory-name">Healthy</span>
                </div>
                <span class="inventory-value">{{ $inventoryCounts['Healthy'] }}</span>
              </div>

              <div class="inventory-legend-item">
                <div class="inventory-legend-left">
                  <span class="inventory-dot" style="background:#f59e0b;"></span>
                  <span class="inventory-name">Low Stock</span>
                </div>
                <span class="inventory-value">{{ $inventoryCounts['Low Stock'] }}</span>
              </div>

              <div class="inventory-legend-item">
                <div class="inventory-legend-left">
                  <span class="inventory-dot" style="background:#ef4444;"></span>
                  <span class="inventory-name">Out of Stock</span>
                </div>
                <span class="inventory-value">{{ $inventoryCounts['Out of Stock'] }}</span>
              </div>

              <div class="inventory-legend-item">
                <div class="inventory-legend-left">
                  <span class="inventory-dot" style="background:#6366f1;"></span>
                  <span class="inventory-name">Overstock</span>
                </div>
                <span class="inventory-value">{{ $inventoryCounts['Overstock'] }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="dashboard-sections">
        <div class="section-card">
          <div class="section-top">
            <div>
              <h3 class="card-title">Analytics</h3>
              <p class="card-sub">Real-time counts based on paid orders.</p>
            </div>
            <span class="section-tag">Analytics</span>
          </div>

          <div class="mini-stats">
            <div class="mini-stat">
              <div class="label">Incoming Orders</div>
              <div class="value">{{ $activeOrders->count() }}</div>
              <div class="hint">Paid but no ETA yet</div>
            </div>

            <div class="mini-stat">
              <div class="label">Preparing</div>
              <div class="value">{{ $preparingOrders->count() }}</div>
              <div class="hint">Orders with ETA</div>
            </div>

            <div class="mini-stat">
              <div class="label">Serving</div>
              <div class="value">{{ $servingOrders->count() }}</div>
              <div class="hint">Ready for handoff</div>
            </div>

            <div class="mini-stat">
              <div class="label">Served</div>
              <div class="value">{{ $servedOrders->count() }}</div>
              <div class="hint">Completed paid orders</div>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-top">
            <div>
              <h3 class="card-title">Sales</h3>
              <p class="card-sub">Fast figures from successful payments.</p>
            </div>
            <span class="section-tag">Sales</span>
          </div>

          <div class="mini-stats">
            <div class="mini-stat">
              <div class="label">Paid Orders Today</div>
              <div class="value">{{ $todayOrdersCount }}</div>
              <div class="hint">Successful paid transactions</div>
            </div>

            <div class="mini-stat">
              <div class="label">All Paid Orders</div>
              <div class="value">{{ $paidOrdersAll->count() }}</div>
              <div class="hint">Overall paid order history</div>
            </div>

            <div class="mini-stat">
              <div class="label">Today Revenue</div>
              <div class="value">₱{{ number_format($todaySales, 2) }}</div>
              <div class="hint">Current day total</div>
            </div>

            <div class="mini-stat">
              <div class="label">Monthly Revenue</div>
              <div class="value">₱{{ number_format($monthSales, 2) }}</div>
              <div class="hint">Current month total</div>
            </div>
          </div>

          <div class="list-box">
            <div class="list-box-title">Top Selling Items</div>
            @if(count($topSelling))
              <ol class="simple-list">
                @foreach($topSelling as $itemName => $qtySold)
                  <li>{{ $itemName }} — {{ $qtySold }} sold</li>
                @endforeach
              </ol>
            @else
              <div class="card-sub">No paid item sales yet.</div>
            @endif
          </div>
        </div>

        <div class="section-card">
          <div class="section-top">
            <div>
              <h3 class="card-title">Inventory</h3>
              <p class="card-sub">Current stock situation from ingredients.</p>
            </div>
            <span class="section-tag">Inventory</span>
          </div>

          <div class="mini-stats">
            <div class="mini-stat">
              <div class="label">Total Ingredients</div>
              <div class="value">{{ $inventoryTotal }}</div>
              <div class="hint">All tracked ingredients</div>
            </div>

            <div class="mini-stat">
              <div class="label">Low Stock</div>
              <div class="value">{{ $inventoryLow }}</div>
              <div class="hint">Need restock soon</div>
            </div>

            <div class="mini-stat">
              <div class="label">Out of Stock</div>
              <div class="value">{{ $inventoryOut }}</div>
              <div class="hint">Unavailable ingredients</div>
            </div>

            <div class="mini-stat">
              <div class="label">Overstock</div>
              <div class="value">{{ $inventoryOver }}</div>
              <div class="hint">Above overstock level</div>
            </div>
          </div>

          <div class="list-box">
            <div class="list-box-title">Critical Stock Alerts</div>
            @if($lowStockItems->count())
              <ul class="simple-list">
                @foreach($lowStockItems as $ingredient)
                  <li>
                    {{ $ingredient->name }} —
                    {{ rtrim(rtrim(number_format((float) $ingredient->stock_qty, 2), '0'), '.') }}
                    {{ $ingredient->unit }}
                  </li>
                @endforeach
              </ul>
            @else
              <div class="card-sub">No low stock ingredients right now.</div>
            @endif
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    function toggleCustomDates(value) {
      document.querySelectorAll('.custom-date-field').forEach(el => {
        el.style.display = value === 'custom' ? 'flex' : 'none';
      });
    }

    const salesLabels = @json($salesLabels);
    const salesData = @json($salesData);
    const expectedLineData = @json($expectedLineData);

    const inventoryLabels = @json(array_keys($inventoryCounts));
    const inventoryData = @json(array_values($inventoryCounts));

    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: {
        labels: salesLabels,
        datasets: [
          {
            label: 'Actual Sales',
            data: salesData,
            borderColor: '#ee9500',
            backgroundColor: 'rgba(238, 149, 0, 0.10)',
            fill: true,
            tension: 0.38,
            borderWidth: 4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#ee9500',
            pointBorderWidth: 2.5
          },
          {
            label: 'Expected Sales',
            data: expectedLineData,
            borderColor: '#d6a84a',
            backgroundColor: 'transparent',
            fill: false,
            tension: 0,
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 0,
            borderDash: [7, 7]
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: { top: 8, right: 8, bottom: 0, left: 0 }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            align: 'center',
            labels: {
              usePointStyle: true,
              boxWidth: 10,
              boxHeight: 10,
              padding: 18,
              color: '#4b5563',
              font: {
                size: 12,
                weight: 'bold'
              }
            }
          },
          tooltip: {
            backgroundColor: '#ffffff',
            titleColor: '#111827',
            bodyColor: '#374151',
            borderColor: '#f3d9a6',
            borderWidth: 1,
            padding: 10,
            displayColors: true
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: '#6b7280',
              font: { weight: 'bold' },
              maxRotation: 0,
              autoSkip: true
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0,0,0,0.05)',
              drawBorder: false
            },
            ticks: {
              color: '#6b7280',
              font: { weight: 'bold' },
              callback: function(value) {
                return '₱' + value;
              }
            }
          }
        }
      }
    });

    new Chart(document.getElementById('inventoryChart'), {
      type: 'doughnut',
      data: {
        labels: inventoryLabels,
        datasets: [{
          data: inventoryData,
          backgroundColor: [
            'rgba(34, 197, 94, 0.92)',
            'rgba(245, 158, 11, 0.92)',
            'rgba(239, 68, 68, 0.92)',
            'rgba(99, 102, 241, 0.92)'
          ],
          borderColor: '#fffaf2',
          borderWidth: 5,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#ffffff',
            titleColor: '#111827',
            bodyColor: '#374151',
            borderColor: '#f3d9a6',
            borderWidth: 1,
            padding: 10
          }
        }
      }
    });
  </script>
</body>
</html>