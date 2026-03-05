<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stock Report (Print)</title>

  <style>
    :root{
      --text:#111;
      --muted:#6b7280;
      --orange:#f59e0b;
      --border: rgba(0,0,0,.08);
      --shadow: 0 10px 30px rgba(0,0,0,.08);
      --radius: 16px;
      --danger:#ef4444;
      --ok:#16a34a;
      --bg:#fff;
    }

    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--text);
      background: var(--bg);
    }

    .page{
      padding: 22px 26px 40px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .top{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap: 14px;
      flex-wrap:wrap;
      margin-bottom: 14px;
    }

    .title{
      font-size: 28px;
      font-weight: 950;
      margin:0;
      letter-spacing: .2px;
    }
    .sub{
      margin-top:6px;
      color: var(--muted);
      font-weight: 700;
      font-size: 13px;
    }
    .tag{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid var(--border);
      background: #fff;
      font-weight: 900;
      font-size: 12px;
      margin-top: 10px;
    }
    .tag b{ color: var(--orange); }

    .btnbar{
      display:flex;
      gap: 10px;
      align-items:center;
    }

    .btn{
      padding: 10px 14px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #fff;
      font-weight: 950;
      cursor:pointer;
      box-shadow: var(--shadow);
      text-decoration:none;
      color:#111;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }
    .btn.primary{
      background: var(--orange);
      border-color: rgba(0,0,0,.08);
    }

    .stats{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin: 14px 0 16px;
    }
    .stat{
      border: 1px solid rgba(245,158,11,.45);
      border-radius: var(--radius);
      padding: 14px;
      box-shadow: var(--shadow);
      background:#fff;
      min-height: 86px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
    }
    .label{
      color: var(--muted);
      font-weight: 900;
      font-size: 12px;
      margin-bottom: 6px;
    }
    .value{
      font-weight: 950;
      font-size: 22px;
      line-height: 1;
    }
    .danger{ color: var(--danger); font-weight: 950; }
    .ok{ color: var(--ok); font-weight: 950; }
    .muted{ color: var(--muted); font-weight: 800; }

    .card{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      background:#fff;
      box-shadow: var(--shadow);
      overflow:hidden;
      margin-top: 14px;
    }
    .cardhead{
      padding: 14px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      background: rgba(245,158,11,.08);
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap: 10px;
      flex-wrap:wrap;
      font-weight: 950;
    }

    table{
      width:100%;
      border-collapse: collapse;
    }
    th, td{
      padding: 11px 12px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      text-align:left;
      font-size: 13px;
      vertical-align: top;
    }
    th{
      background:#fff;
      font-weight: 950;
    }

    .pill{
      display:inline-flex;
      align-items:center;
      padding: 5px 10px;
      border-radius: 999px;
      border: 1px solid rgba(0,0,0,.08);
      font-weight: 950;
      font-size: 12px;
      background:#fff;
      white-space:nowrap;
    }
    .pill.low{ color: var(--danger); border-color: rgba(239,68,68,.25); }
    .pill.ok{ color: var(--ok); border-color: rgba(22,163,74,.25); }

    .note{
      padding: 12px 14px;
      border-top: 1px solid rgba(0,0,0,.06);
      font-weight: 900;
    }

    /* Print rules */
    @media print{
      .btnbar{ display:none !important; }
      body{ background:#fff; }
      .page{ max-width: none; padding: 0; }
      .card, .stat{ box-shadow:none !important; }
    }

    @media (max-width: 1000px){
      .stats{ grid-template-columns: repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 720px){
      .stats{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
@php
  $rangeLabel    = $rangeLabel ?? '';
  $generatedAt   = $generatedAt ?? now()->format('Y-m-d h:i A');

  $from          = $from ?? '';
  $to            = $to ?? '';
  $q             = $q ?? '';
  $status        = $status ?? 'all';

  $filteredTotal = $filteredTotal ?? 0;
  $lowStockCount = $lowStockCount ?? 0;

  // inventory snapshot (current stock)
  $ingredients   = $ingredients ?? collect();

  // consumption summary
  $consumedRows  = $consumedRows ?? collect();
  $consumedCount = $consumedCount ?? 0;
  $consumedTotal = $consumedTotal ?? 0;

  // movement history
  $movements     = $movements ?? collect();
  $movementCount = $movementCount ?? 0;
@endphp

<div class="page">

  <div class="top">
    <div>
      <h1 class="title">99 Silog Cafe — Stock Report</h1>
      <div class="sub">{{ $rangeLabel }} • Generated: {{ $generatedAt }}</div>

      <div class="tag">
        <b>From:</b> {{ $from !== '' ? $from : '(auto)' }}
        • <b>To:</b> {{ $to !== '' ? $to : '(auto)' }}
        • <b>Search:</b> {{ $q !== '' ? $q : '(none)' }}
        • <b>Status:</b> {{ strtoupper($status) }}
      </div>
    </div>

    <div class="btnbar">
      <button class="btn primary" onclick="window.print()">Print / Save as PDF</button>
      <a class="btn" href="{{ url()->previous() }}">Back</a>
    </div>
  </div>

  <section class="stats">
    <div class="stat">
      <div>
        <div class="label">Filtered Results</div>
        <div class="value">{{ (int)$filteredTotal }}</div>
      </div>
    </div>

    <div class="stat">
      <div>
        <div class="label">Low/Out (filtered)</div>
        <div class="value {{ (int)$lowStockCount > 0 ? 'danger' : 'ok' }}">{{ (int)$lowStockCount }}</div>
      </div>
    </div>

    <div class="stat">
      <div>
        <div class="label">Total Consumed (OUT)</div>
        <div class="value danger">{{ number_format((float)$consumedTotal, 2) }}</div>
      </div>
    </div>
  </section>

  <!-- ✅ Inventory Snapshot (Current Stock) -->
  <section class="card">
    <div class="cardhead">
      <div>Inventory Snapshot (Current Stock)</div>
      <div class="muted">Filtered view</div>
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
              $stock = (float)($ing->stock_qty ?? 0);
              $reorder = (float)($ing->reorder_level ?? 0);
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
              <td colspan="4" class="muted" style="text-align:center; padding: 16px;">
                No ingredients found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="note">
      @if((int)$lowStockCount > 0)
        <span class="danger">⚠ Low/Out stock detected in current filter.</span>
      @else
        <span class="ok">✅ No low/out items in current filter.</span>
      @endif
    </div>
  </section>



  <!-- ✅ Stock Movements (History) -->
  <section class="card">
    <div class="cardhead">
      <div>Stock Movements (History)</div>
      <div class="muted">Rows: {{ (int)$movementCount }}</div>
    </div>

    <div style="overflow:auto;">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Ingredient</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Reason</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movements as $m)
            @php
              $t = strtolower((string)($m->type ?? ''));
              $qty = (float)($m->qty ?? 0);
            @endphp
            <tr>
              <td class="muted">{{ optional($m->created_at)->format('Y-m-d h:i A') }}</td>
              <td><strong>{{ $m->ingredient->name ?? 'Ingredient' }}</strong></td>
  <td>
  @if($t === 'out')
    <span class="pill low">OUT</span>
  @elseif($t === 'in')
    <span class="pill ok">IN</span>
  @else
    <span class="pill">{{ strtoupper($t) }}</span>
  @endif
</td>
           <td class="{{ $t === 'out' ? 'danger' : ($t === 'in' ? 'ok' : '') }}">
  {{ ($t === 'in' ? '+' : ($t === 'out' ? '-' : '')) }}
  {{ number_format($qty, 2) }} {{ $m->ingredient->unit ?? '' }}
</td>
              <td class="muted">{{ $m->reason ?? '' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="muted" style="text-align:center; padding: 16px;">
                No stock movements found in this date range.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="note">
      <span class="muted">Tip:</span> Use From–To to view movement history and consumption for that range.
    </div>
  </section>

    <!-- ✅ Ingredients Consumed Summary (NEW) -->
  <section class="card">
    <div class="cardhead">
      <div>Ingredients Consumed (Summary)</div>
      <div class="muted">Sum of OUT movements in range • Items: {{ (int)$consumedCount }}</div>
    </div>

    <div style="padding:12px 14px;">
      <div class="muted">
        Total consumed qty (sum of OUT): <strong style="color:#111;">{{ number_format((float)$consumedTotal, 2) }}</strong>
      </div>
    </div>

    <div style="overflow:auto;">
      <table>
        <thead>
          <tr>
            <th style="width:55%;">Ingredient</th>
            <th style="width:15%;">Unit</th>
            <th>Qty Used</th>
          </tr>
        </thead>
        <tbody>
          @forelse($consumedRows as $r)
            <tr>
              <td><strong>{{ $r->ingredient_name }}</strong></td>
              <td class="muted">{{ $r->unit }}</td>
              <td class="danger">{{ number_format((float)($r->qty_used ?? 0), 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="muted" style="text-align:center; padding: 16px;">
                No consumption found (no OUT movements) in this date range.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="note">
      {{-- <span class="muted">Note:</span> This table is computed from <strong>stock_movements</strong> where type = <strong>OUT</strong>. --}}
    </div>
  </section>

</div>
</body>
</html>