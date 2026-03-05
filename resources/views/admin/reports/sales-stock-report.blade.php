<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stock Reports</title>

  <style>
    :root{
      --sidebar:#e4e3e3;
      --text:#222;
      --muted:#6b7280;
      --orange:#f59e0b;
      --card:#fff;
      --shadow-soft:0 10px 30px rgba(0,0,0,.08);
      --radius:16px;
      --danger:#ef4444;
      --ok:#16a34a;
    }
    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background:#fff;
      color:var(--text);
    }

    .shell{ width:100%; min-height:100vh; display:grid; grid-template-columns:240px 1fr; }
    .sidebar{ background:var(--sidebar); padding:18px 14px; border-right:1px solid rgba(0,0,0,.06); }
    .brand{ display:flex; justify-content:center; padding:6px 6px 14px; }
    .logo-box{
      width:120px; height:58px; background:#fff; border-radius:10px;
      display:flex; align-items:center; justify-content:center; overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{ width:100%; height:100%; object-fit:contain; padding:6px; }

    .side-section-title{
      font-size:11px; font-weight:800; color:#1f2937;
      margin:14px 6px 8px; text-transform:uppercase; opacity:.85;
    }
    .nav{ display:flex; flex-direction:column; gap:8px; padding:0 6px; }
    .nav a{
      text-decoration:none; font-size:13px;
      padding:10px 12px; border-radius:999px;
      color:#111; background:rgba(255,255,255,.55);
      border:1px solid rgba(0,0,0,.04); transition:.15s ease;
    }
    .nav a:hover{ background:rgba(255,184,30,.25); }
    .nav a.active{
      background:var(--orange); font-weight:900;
      box-shadow:0 6px 14px rgba(0,0,0,.12);
    }

    .content{ padding:22px 24px; }

    .header{ display:flex; justify-content:space-between; gap:16px; margin-bottom:16px; }
    .title{ font-size:26px; font-weight:900; color:var(--orange); margin:0; }
    .subtitle{ margin-top:6px; font-size:13px; color:var(--muted); font-weight:700; }

    .logout-btn{
      padding:10px 16px; background:var(--orange);
      border:none; border-radius:12px; cursor:pointer; font-weight:900;
      box-shadow:0 8px 20px rgba(0,0,0,.12);
    }

    .filter{
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:var(--radius);
      box-shadow:var(--shadow-soft);
      padding:12px 14px;
      display:flex;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      margin-bottom:16px;
    }
    .filter form{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
    }
    .filter label{
      font-size:12px;
      font-weight:900;
      color:var(--muted);
    }

    input[type="date"], select, input[type="text"]{
      padding:9px 10px;
      border-radius:12px;
      border:1px solid rgba(0,0,0,.14);
      outline:none;
      font-weight:900;
      font-size:12.5px;
      background:#fff;
    }
    input[type="text"]{ min-width:220px; }

    .btn{
      padding:10px 16px;
      background:var(--orange);
      border-radius:12px;
      color:#111;
      font-weight:900;
      border:none;
      cursor:pointer;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }
    .btn.secondary{
      background:#fff;
      border:1px solid rgba(0,0,0,.12);
      box-shadow:0 6px 14px rgba(0,0,0,.06);
    }

    .stats{
      display:grid;
      grid-template-columns:repeat(3, minmax(0,1fr));
      gap:14px;
      margin-bottom:16px;
    }
    .stat{
      background:#fff;
      border:1px solid rgba(245,158,11,.50);
      border-radius:var(--radius);
      padding:14px;
      box-shadow:var(--shadow-soft);
      min-height:86px;
      display:flex;
      align-items:center;
      justify-content:space-between;
    }
    .stat .label{ font-size:12px; color:var(--muted); font-weight:900; margin-bottom:6px; }
    .stat .value{ font-size:22px; font-weight:900; }
    .danger{ color:var(--danger); font-weight:900; }
    .oktxt{ color:var(--ok); font-weight:900; }

    .card{
      background:#fff;
      border:1px solid rgba(0,0,0,.06);
      border-radius:var(--radius);
      box-shadow:var(--shadow-soft);
      overflow:hidden;
      margin-bottom:16px;
    }
    .card-header{
      padding:14px;
      border-bottom:1px solid rgba(0,0,0,.06);
      background:rgba(245,158,11,.08);
      display:flex;
      justify-content:space-between;
      align-items:center;
      flex-wrap:wrap;
      gap:12px;
      font-weight:900;
    }
    .muted{ color:var(--muted); font-weight:800; }

    table{ width:100%; border-collapse:collapse; }
    th, td{
      padding:12px 14px;
      border-bottom:1px solid rgba(0,0,0,.06);
      text-align:left;
      font-size:13px;
      vertical-align:top;
    }
    th{ font-weight:900; color:#111; background:#fff; }

    .pill{
      display:inline-flex;
      align-items:center;
      padding:6px 10px;
      border-radius:999px;
      border:1px solid rgba(0,0,0,.08);
      font-size:12px;
      font-weight:900;
      background:#fff;
      white-space:nowrap;
    }
    .pill.ok{ border-color:rgba(22,163,74,.25); color:var(--ok); }
    .pill.low{ border-color:rgba(239,68,68,.25); color:var(--danger); }

    /* ✅ Pagination (inventory style) */
    .pager{
      padding:14px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:14px;
      flex-wrap:wrap;
    }
    .pager .left{ color:var(--muted); font-weight:900; font-size:13px; }
    .pager .right{ display:flex; flex-direction:column; align-items:flex-end; gap:10px; }
    .pager .top-controls{ display:flex; gap:10px; }
    .page-btn{
      padding:10px 14px;
      border-radius:14px;
      border:1px solid rgba(0,0,0,.12);
      background:#fff;
      font-weight:900;
      cursor:pointer;
      text-decoration:none;
      color:#111;
    }
    .page-btn.disabled{ opacity:.45; pointer-events:none; }
    .pages{ display:flex; gap:8px; align-items:center; }
    .page-num{
      min-width:44px; height:44px;
      display:flex; align-items:center; justify-content:center;
      border-radius:14px;
      border:1px solid rgba(0,0,0,.12);
      background:#fff;
      font-weight:900;
      text-decoration:none;
      color:#111;
    }
    .page-num.active{ background:var(--orange); border-color:rgba(245,158,11,.40); }
    .page-num.disabled{ opacity:.45; pointer-events:none; }
    .dots{ padding:0 8px; font-weight:900; color:var(--muted); }

    @media (max-width:1100px){
      .stats{ grid-template-columns:repeat(2, minmax(0,1fr)); }
    }
    @media (max-width:720px){
      .shell{ grid-template-columns:1fr; }
      .sidebar{ display:none; }
      .stats{ grid-template-columns:1fr; }
      .pager{ justify-content:center; }
      .pager .right{ align-items:center; width:100%; }
      .pager .left{ width:100%; text-align:center; }
    }
  </style>
</head>

<body>
@php
  $from = $from ?? '';
  $to = $to ?? '';
  $q = $q ?? '';
  $status = $status ?? 'all';
  $rangeLabel = $rangeLabel ?? '';
  $filteredTotal = $filteredTotal ?? 0;
  $lowStockCount = $lowStockCount ?? 0;
  $movementCount = $movementCount ?? 0;

  // keep current query params for downloads
  $qs = request()->query();
@endphp

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
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Order Management</a>
      <a href="{{ route('admin.table-management') }}" class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">Table Management</a>
      <a href="{{ route('admin.daily-sales-report') }}" class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">Daily Sales Report</a>
    </nav>

    <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
    <nav class="nav">
      <a href="{{ route('admin.menu-management') }}" class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">Menu Management</a>
      <a href="{{ route('admin.feedbacks') }}" class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">Feedback Management</a>
      <a href="{{ route('admin.inventory') }}" class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">Inventory Management</a>
      <a href="{{ route('admin.sales-stock-reports') }}" class="active">Stock Reports</a>
    </nav>
  </aside>

  <main class="content">
    <div class="header">
      <div>
        <h1 class="title">Stock Reports</h1>
        <div class="subtitle">Inventory snapshot (current) + Stock movements (history)</div>
      </div>

      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout-btn" type="submit">Log Out</button>
      </form>
    </div>

    <!-- Filter -->
    <div class="filter">
      <div class="muted">
        <strong style="color:#111;">{{ $rangeLabel }}</strong>
        <div style="margin-top:4px;">
          Tip: <strong style="color:#111;">From/To filters the Stock Movements history</strong>. Inventory table is the current snapshot.
        </div>
      </div>

      <form method="GET" action="{{ route('admin.sales-stock-reports') }}">
        <label for="from">From</label>
        <input id="from" type="date" name="from" value="{{ $from }}">

        <label for="to">To</label>
        <input id="to" type="date" name="to" value="{{ $to }}">

        <label for="q">Search</label>
        <input id="q" type="text" name="q" value="{{ $q }}" placeholder="Search ingredient...">

        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
          <option value="ok"  {{ $status === 'ok' ? 'selected' : '' }}>OK</option>
          <option value="low" {{ $status === 'low' ? 'selected' : '' }}>Low</option>
          <option value="out" {{ $status === 'out' ? 'selected' : '' }}>Out</option>
        </select>

        <button class="btn" type="submit">Apply</button>
        <a class="btn secondary" href="{{ route('admin.sales-stock-reports') }}">Clear</a>

        <a class="btn" href="{{ route('admin.sales-stock-reports.print', $qs) }}">Download PDF</a>
        <a class="btn" href="{{ route('admin.sales-stock-reports.export.csv', $qs) }}">Download CSV</a>
      </form>
    </div>

    <!-- Stats -->
    <section class="stats">
      <div class="stat">
        <div>
          <div class="label">Filtered Ingredients</div>
          <div class="value">{{ $filteredTotal }}</div>
        </div>
      </div>

      <div class="stat">
        <div>
          <div class="label">Low/Out (ingredients)</div>
          <div class="value {{ $lowStockCount > 0 ? 'danger' : 'oktxt' }}">{{ $lowStockCount }}</div>
        </div>
      </div>

      <div class="stat">
        <div>
          <div class="label">Movements in Range</div>
          <div class="value">{{ $movementCount }}</div>
        </div>
      </div>
    </section>

    <!-- ✅ Inventory Snapshot -->
    <section class="card">
      <div class="card-header">
        <div>Inventory Snapshot (Current)</div>
        <div class="muted">10 per page</div>
      </div>

      <div style="overflow:auto;">
        <table>
          <thead>
            <tr>
              <th>Ingredient</th>
              <th>Stock</th>
              <th>Reorder</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ingredients as $ing)
              @php
                $stock = (float) ($ing->stock_qty ?? 0);
                $reorder = (float) ($ing->reorder_level ?? 0);
                $isOut = $stock <= 0;
                $isLow = (!$isOut && $stock <= $reorder);
              @endphp
              <tr>
                <td>
                  <strong>{{ $ing->name }}</strong>
                  <div class="muted" style="margin-top:4px;">Unit: {{ $ing->unit }}</div>
                </td>
                <td class="{{ ($isOut || $isLow) ? 'danger' : '' }}">{{ number_format($stock, 2) }}</td>
                <td class="muted">{{ number_format($reorder, 2) }}</td>
                <td>
                  @if($isOut)
                    <span class="pill low">OUT</span>
                  @elseif($isLow)
                    <span class="pill low">LOW</span>
                  @else
                    <span class="pill ok">OK</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="muted" style="text-align:center; padding:18px;">
                  No ingredients found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ✅ Inventory Pagination --}}
      @if($ingredients instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pager">
          <div class="left">
            Showing {{ $ingredients->firstItem() ?? 0 }} - {{ $ingredients->lastItem() ?? 0 }} of {{ $ingredients->total() }} items
          </div>

          <div class="right">
            <div class="top-controls">
              <a class="page-btn {{ $ingredients->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $ingredients->previousPageUrl() ?? '#' }}">« Previous</a>
              <a class="page-btn {{ $ingredients->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $ingredients->nextPageUrl() ?? '#' }}">Next »</a>
            </div>

            <div class="pages">
              @php
                $current = $ingredients->currentPage();
                $last = $ingredients->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
                if ($end - $start < 4) {
                  $start = max(1, $end - 4);
                  $end = min($last, $start + 4);
                }
              @endphp

              <a class="page-num {{ $ingredients->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $ingredients->previousPageUrl() ?? '#' }}">‹</a>

              @if($start > 1)
                <a class="page-num" href="{{ $ingredients->url(1) }}">1</a>
                @if($start > 2) <span class="dots">…</span> @endif
              @endif

              @for($p = $start; $p <= $end; $p++)
                <a class="page-num {{ $p === $current ? 'active' : '' }}" href="{{ $ingredients->url($p) }}">{{ $p }}</a>
              @endfor

              @if($end < $last)
                @if($end < $last - 1) <span class="dots">…</span> @endif
                <a class="page-num" href="{{ $ingredients->url($last) }}">{{ $last }}</a>
              @endif

              <a class="page-num {{ $ingredients->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $ingredients->nextPageUrl() ?? '#' }}">›</a>
            </div>
          </div>
        </div>
      @endif

      <div style="padding:12px 14px;">
        @if($lowStockCount > 0)
          <div class="danger">⚠ Low/Out stock detected in current ingredient filter.</div>
        @else
          <div class="oktxt">✅ No low/out items in current ingredient filter.</div>
        @endif
      </div>
    </section>

    

    <!-- ✅ Stock Movements (History) -->
    <section class="card">
      <div class="card-header">
        <div>Stock Movements (History)</div>
        <div class="muted">Filtered by From/To • 10 per page</div>
      </div>

      <div style="overflow:auto;">
        <table>
          <thead>
            <tr>
              <th style="width:22%;">Date</th>
              <th style="width:28%;">Ingredient</th>
              <th style="width:12%;">Type</th>
              <th style="width:14%;">Qty</th>
              <th>Reason</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movements as $m)
              @php
                $type = strtolower((string)($m->type ?? ''));
                $isIn = $type === 'in';
                $isOut = $type === 'out';
              @endphp
              <tr>
                <td class="muted">{{ optional($m->created_at)->format('M d, Y h:i A') }}</td>
                <td><strong>{{ $m->ingredient->name ?? 'Ingredient' }}</strong></td>
                <td>
                  @if($isIn)
                    <span class="pill ok">IN</span>
                  @elseif($isOut)
                    <span class="pill low">OUT</span>
                  @else
                    <span class="pill">ADJUST</span>
                  @endif
                </td>
               <td class="{{ $isOut ? 'danger' : ($isIn ? 'oktxt' : '') }}">
  {{ $isIn ? '+' : ($isOut ? '-' : '') }}
  {{ number_format((float)($m->qty ?? 0), 2) }} {{ $m->ingredient->unit ?? '' }}
</td>
                <td class="muted">{{ $m->reason ?? '' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="muted" style="text-align:center; padding:18px;">
                  No stock movements found in this date range.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ✅ Movements Pagination --}}
      @if($movements instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pager">
          <div class="left">
            Showing {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} of {{ $movements->total() }} movements
          </div>

          <div class="right">
            <div class="top-controls">
              <a class="page-btn {{ $movements->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $movements->previousPageUrl() ?? '#' }}">« Previous</a>
              <a class="page-btn {{ $movements->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $movements->nextPageUrl() ?? '#' }}">Next »</a>
            </div>

            <div class="pages">
              @php
                $current = $movements->currentPage();
                $last = $movements->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
                if ($end - $start < 4) {
                  $start = max(1, $end - 4);
                  $end = min($last, $start + 4);
                }
              @endphp

              <a class="page-num {{ $movements->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $movements->previousPageUrl() ?? '#' }}">‹</a>

              @if($start > 1)
                <a class="page-num" href="{{ $movements->url(1) }}">1</a>
                @if($start > 2) <span class="dots">…</span> @endif
              @endif

              @for($p = $start; $p <= $end; $p++)
                <a class="page-num {{ $p === $current ? 'active' : '' }}" href="{{ $movements->url($p) }}">{{ $p }}</a>
              @endfor

              @if($end < $last)
                @if($end < $last - 1) <span class="dots">…</span> @endif
                <a class="page-num" href="{{ $movements->url($last) }}">{{ $last }}</a>
              @endif

              <a class="page-num {{ $movements->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $movements->nextPageUrl() ?? '#' }}">›</a>
            </div>
          </div>
        </div>
      @endif
    </section>

        <!-- ✅ Ingredients Consumed Summary -->
    <section class="card">
      <div class="card-header">
        <div>Ingredients Consumed (Summary)</div>
        <div class="muted">Sum of <strong>OUT</strong> movements in date range • 10 per page</div>
      </div>

      <div style="padding:12px 14px;">
        <div class="muted">
          Total consumed qty (sum of OUT): <strong style="color:#111;">{{ number_format((float)($consumedTotal ?? 0), 2) }}</strong>
          • Items: <strong style="color:#111;">{{ (int)($consumedCount ?? 0) }}</strong>
        </div>
      </div>

      <div style="overflow:auto;">
        <table>
          <thead>
            <tr>
              <th style="width:45%;">Ingredient</th>
              <th style="width:20%;">Unit</th>
              <th>Qty Used</th>
            </tr>
          </thead>
          <tbody>
            @forelse($consumedRows as $r)
              <tr>
                <td><strong>{{ $r->ingredient_name }}</strong></td>
                <td class="muted">{{ $r->unit }}</td>
                <td class="danger">{{ number_format((float)$r->qty_used, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="muted" style="text-align:center; padding:18px;">
                  No consumption found (no OUT movements) in this date range.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ✅ Consumed pagination --}}
      @if($consumedRows instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="pager">
          <div class="left">
            Showing {{ $consumedRows->firstItem() ?? 0 }} - {{ $consumedRows->lastItem() ?? 0 }} of {{ $consumedRows->total() }} items
          </div>

          <div class="right">
            <div class="top-controls">
              <a class="page-btn {{ $consumedRows->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $consumedRows->previousPageUrl() ?? '#' }}">« Previous</a>
              <a class="page-btn {{ $consumedRows->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $consumedRows->nextPageUrl() ?? '#' }}">Next »</a>
            </div>

            <div class="pages">
              @php
                $current = $consumedRows->currentPage();
                $last = $consumedRows->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
                if ($end - $start < 4) {
                  $start = max(1, $end - 4);
                  $end = min($last, $start + 4);
                }
              @endphp

              <a class="page-num {{ $consumedRows->onFirstPage() ? 'disabled' : '' }}"
                 href="{{ $consumedRows->previousPageUrl() ?? '#' }}">‹</a>

              @if($start > 1)
                <a class="page-num" href="{{ $consumedRows->url(1) }}">1</a>
                @if($start > 2) <span class="dots">…</span> @endif
              @endif

              @for($p = $start; $p <= $end; $p++)
                <a class="page-num {{ $p === $current ? 'active' : '' }}" href="{{ $consumedRows->url($p) }}">{{ $p }}</a>
              @endfor

              @if($end < $last)
                @if($end < $last - 1) <span class="dots">…</span> @endif
                <a class="page-num" href="{{ $consumedRows->url($last) }}">{{ $last }}</a>
              @endif

              <a class="page-num {{ $consumedRows->hasMorePages() ? '' : 'disabled' }}"
                 href="{{ $consumedRows->nextPageUrl() ?? '#' }}">›</a>
            </div>
          </div>
        </div>
      @endif
    </section>

  </main>
</div>
</body>
</html>