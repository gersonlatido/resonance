<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daily Sales Report</title>

  <style>
    :root{
      --panel: #ffffff;
      --sidebar: #e4e3e3;
      --text: #222;
      --muted: #6b7280;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --card: #fff;
      --border: #e5e7eb;
      --shadow-soft: 0 10px 30px rgba(0,0,0,.08);
      --radius: 16px;
    }

    * { box-sizing: border-box; }

    body{
      margin:0;
      font-family: 'Figtree', sans-serif;
      background: #ffffff;
      color: var(--text);
    }

    /* Layout */
    .shell{
      width: 100%;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 240px 1fr;
    }

    /* Sidebar */
    .sidebar{
      background: var(--sidebar);
      padding: 18px 14px;
      border-right: 1px solid rgba(0,0,0,.06);
    }

    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 6px 6px 14px 6px;
    }

    .logo-box{
      width: 120px;
      height: 58px;
      background: #fff;
      border-radius: 10px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 6px;
    }

    .side-section-title{
      font-size: 11px;
      font-weight: 800;
      color: #1f2937;
      margin: 14px 6px 8px;
      text-transform: uppercase;
      opacity: .85;
    }

    .nav{
      display:flex;
      flex-direction:column;
      gap: 8px;
      padding: 0 6px;
    }

    .nav a{
      text-decoration:none;
      font-size: 13px;
      padding: 10px 12px;
      border-radius: 999px;
      color:#111;
      display:flex;
      align-items:center;
      gap: 8px;
      transition: .15s ease;
      background: rgba(255,255,255,.55);
      border: 1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background: rgba(255, 184, 30, 0.25); }

    .nav a.active{
      background: var(--orange);
      color:#111;
      font-weight: 800;
      border-color: rgba(0,0,0,.06);
      box-shadow: 0 6px 14px rgba(0,0,0,.12);
    }

    /* Main content */
    .content{
      padding: 22px 24px;
      background: #ffffff;
    }

    .header{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap: 16px;
      margin-bottom: 16px;
    }

    .header-left .title{
      font-size: 26px;
      font-weight: 900;
      color: var(--orange);
      letter-spacing: .2px;
      margin: 0;
    }
    .header-left .subtitle{
      margin-top: 6px;
      font-size: 13px;
      color: var(--muted);
      font-weight: 600;
    }

    .logout-btn{
      padding: 10px 16px;
      background: var(--orange);
      border: none;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 900;
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
    }
    .logout-btn:hover{ filter: brightness(.97); }

    /* Cards */
    .stats{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 16px;
    }

    .stat{
      background: var(--card);
      border: 1px solid rgba(245,158,11,.50);
      border-radius: var(--radius);
      padding: 14px;
      box-shadow: var(--shadow-soft);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      min-height: 86px;
    }

    .stat .label{
      font-size: 12px;
      color: var(--muted);
      font-weight: 800;
      margin-bottom: 6px;
    }
    .stat .value{
      font-size: 22px;
      font-weight: 900;
      color:#111;
      line-height: 1;
    }

    /* Filter */
    .filter{
      background:#fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
      padding: 12px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      flex-wrap:wrap;
      margin-bottom: 16px;
    }

    .filter form{
      display:flex;
      align-items:center;
      gap: 10px;
      flex-wrap:wrap;
    }

    .filter label{
      font-size: 12px;
      font-weight: 800;
      color: var(--muted);
    }

    input[type="date"]{
      padding: 9px 10px;
      border-radius: 12px;
      border: 1px solid rgba(0,0,0,.14);
      outline:none;
      font-weight: 700;
      font-size: 12.5px;
      background:#fff;
    }

   .btn{
  padding: 10px 16px;
  background: #f59e0b;
  border-radius: 12px;
  text-decoration: none;
  color: #111;
  font-weight: 900;
  border: none;
  display:inline-block;
}
.btn:hover{
  opacity: 0.9;
}

    /* Table */
    .table-card{
      background: #fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
      overflow:hidden;
    }

    .table-card-header{
      padding: 14px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      background: linear-gradient(0deg, rgba(245,158,11,.08), rgba(245,158,11,.08));
      font-weight: 900;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap: 12px;
      flex-wrap:wrap;
    }

    .muted{ color: var(--muted); font-weight: 700; }

    table{
      width:100%;
      border-collapse: collapse;
    }

    th, td{
      padding: 12px 14px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      text-align:left;
      font-size: 13px;
    }

    th{
      background:#fff;
      font-weight: 900;
      color:#111;
    }

    .money{
      font-weight: 900;
      color: var(--orange-2);
    }

    /* Responsive */
    @media (max-width: 1100px){
      .stats{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 720px){
      .shell{ grid-template-columns: 1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="shell">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="brand">
        <div class="logo-box">
          <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
        </div>
      </div>

      <div class="side-section-title">Cashier Transaction</div>
      <nav class="nav">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          Order Management
        </a>

        <a href="{{ route('admin.table-management') }}"
           class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">
          Table Management
        </a>

        <a href="{{ route('admin.daily-sales-report') }}"
           class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">
           Sales Report
        </a>
      </nav>

      <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
      <nav class="nav">
        <a href="{{ route('admin.menu-management') }}"
           class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">
          Menu Management
        </a>

        <a href="{{ route('admin.feedbacks') }}"
           class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">
          Feedback Management
        </a>

        <a href="{{ route('admin.inventory') }}"
           class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
          Inventory Management
        </a>

         <a href="{{ route('admin.sales-stock-reports') }}"
            class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">
             Sales and Stock Reports
         </a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="content">

      <!-- Header -->
      <div class="header">
        <div class="header-left">
          <h1 class="title">Daily Sales Report</h1>
          <div class="subtitle">Shows paid orders (Xendit) for the selected date</div>
        </div>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="logout-btn" type="submit">Log Out</button>
        </form>
      </div>

      <!-- Filter -->
   <div class="filter">
  <div class="muted">
    <strong style="color:#111;">{{ $rangeLabel ?? ('Daily (' . $selectedDate . ')') }}</strong>
    <div style="margin-top:4px;">Selected Date: <strong style="color:#111;">{{ $selectedDate }}</strong></div>
  </div>

  <form method="GET" action="{{ route('admin.daily-sales-report') }}">
    <label for="period">Period</label>
    <select id="period" name="period" style="padding:9px 10px;border-radius:12px;border:1px solid rgba(0,0,0,.14);font-weight:800;font-size:12.5px;">
      <option value="daily"  {{ ($period ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
      <option value="weekly" {{ ($period ?? 'daily') === 'weekly' ? 'selected' : '' }}>Weekly</option>
      <option value="monthly"{{ ($period ?? 'daily') === 'monthly' ? 'selected' : '' }}>Monthly</option>
      <option value="yearly" {{ ($period ?? 'daily') === 'yearly' ? 'selected' : '' }}>Yearly</option>
    </select>

    <label for="date">Pick date</label>
    <input id="date" type="date" name="date" value="{{ $selectedDate }}">

    <button class="btn" type="submit">Apply</button>

    <!-- ✅ PDF (no package): open print view then user saves PDF -->
   <div style="display:flex; gap:10px; flex-wrap:wrap;">

    <!-- ✅ Download PDF -->
    <a class="btn"
       href="{{ route('admin.sales-report.print', ['period' => $period, 'date' => $selectedDate]) }}">
        Download PDF
    </a>

    <!-- ✅ Download Styled Excel -->
    <a class="btn"
       href="{{ route('admin.sales-report.export.xls', ['period' => $period, 'date' => $selectedDate]) }}">
        Download Excel
    </a>

</div>
  </form>
</div>

      <!-- Stats -->
      <section class="stats" aria-label="Sales stats">
        <div class="stat">
          <div>
            <div class="label">Total Sales (Paid)</div>
            <div class="value money">₱{{ number_format($totalSales, 2) }}</div>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Paid Orders</div>
            <div class="value">{{ $paidCount }}</div>
          </div>
        </div>

        <div class="stat">
          <div>
            <div class="label">Average Order Value</div>
            <div class="value">₱{{ number_format($avgOrder, 2) }}</div>
          </div>
        </div>
      </section>

      <!-- Table -->
      <section class="table-card">
        <div class="table-card-header">
          <div>Paid Orders List</div>
          <div class="muted">Sorted: Newest first</div>
        </div>

        <div style="overflow:auto;">
          <table>
            <thead>
              <tr>
                <th>Order Code</th>
                <th>Table</th>
                <th>Total</th>
                <th>Status</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($paidOrders as $order)
                <tr>
                  <td><strong>{{ $order->order_code }}</strong></td>
                  <td class="muted">Table {{ $order->table_number }}</td>
                  <td class="money">₱{{ number_format((float)$order->total, 2) }}</td>
                  <td class="muted">{{ ucfirst($order->status) }}</td>
                  <td class="muted">{{ optional($order->created_at)->format('h:i A') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="muted" style="text-align:center; padding: 18px;">
                    No paid orders found for this date.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>
</body>
</html>