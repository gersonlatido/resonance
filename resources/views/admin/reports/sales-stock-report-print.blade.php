@php
  $lowStockCount = isset($lowStock) ? count($lowStock) : 0;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sales & Stock Report</title>

  <style>
    :root{
      --text:#111;
      --muted:#6b7280;
      --orange:#f59e0b;
      --orange2:#ffb300;
      --border: rgba(0,0,0,.08);
      --shadow: 0 10px 30px rgba(0,0,0,.08);
      --radius: 16px;
      --danger:#ef4444;
      --ok:#16a34a;
      --bg:#fff;
      --soft:#fafafa;
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
    }
    .btn.primary{
      background: var(--orange);
      border-color: rgba(0,0,0,.08);
    }

    .stats{
      display:grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
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
    .money{ color: var(--orange2); }
    .danger{ color: var(--danger); }
    .ok{ color: var(--ok); }

    .grid{
      display:grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 14px;
      align-items:start;
    }

    .card{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      background:#fff;
      box-shadow: var(--shadow);
      overflow:hidden;
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
    .muted{ color: var(--muted); font-weight: 800; }

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

    .section{
      margin-top: 14px;
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
      .grid{ grid-template-columns: 1fr; }
    }
       .btn.secondary{ background:#fff; color:#111; }
       a.btn.secondary{
         text-decoration: none;
         display:inline-block;
       }
  </style>
</head>

<body>
  <div class="page">

    <div class="top">
      <div>
        <h1 class="title">99 Silog Cafe — Sales & Stock Report</h1>
        <div class="sub">{{ $rangeLabel }} • Generated: {{ $generatedAt ?? now()->format('Y-m-d h:i A') }}</div>
        <div class="tag"><b>Period:</b> {{ ucfirst($period ?? 'daily') }} • <b>Date:</b> {{ $selectedDate ?? '' }}</div>
      </div>

      <div class="btnbar">
        <button class="btn primary" onclick="window.print()">Print / Save as PDF</button>
         <a class="btn secondary" href="{{ url()->previous() }}">Back</a>
      </div>
    </div>

    <section class="stats">
      <div class="stat">
        <div>
          <div class="label">Total Sales (Paid)</div>
          <div class="value money">₱{{ number_format($totalSales ?? 0, 2) }}</div>
        </div>
      </div>

      <div class="stat">
        <div>
          <div class="label">Paid Orders</div>
          <div class="value">{{ (int)($paidCount ?? 0) }}</div>
        </div>
      </div>

      <div class="stat">
        <div>
          <div class="label">Average Order</div>
          <div class="value">₱{{ number_format($avgOrder ?? 0, 2) }}</div>
        </div>
      </div>

      <div class="stat">
        <div>
          <div class="label">Low Stock Items</div>
          <div class="value {{ $lowStockCount > 0 ? 'danger' : 'ok' }}">{{ $lowStockCount }}</div>
        </div>
      </div>
    </section>

    <section class="grid">
      <!-- Paid Orders -->
      <div class="card">
        <div class="cardhead">
          <div>Paid Orders</div>
          <div class="muted">Newest first</div>
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
              @forelse($paidOrders as $o)
                <tr>
                  <td><strong>{{ $o->order_code }}</strong></td>
                  <td class="muted">Table {{ $o->table_number }}</td>
                  <td class="money">₱{{ number_format((float)$o->total, 2) }}</td>
                  <td class="muted">{{ ucfirst($o->status) }}</td>
                  <td class="muted">{{ optional($o->created_at)->format('Y-m-d h:i A') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="muted" style="text-align:center; padding: 16px;">
                    No paid orders found for this period.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Inventory -->
      <div class="card">
        <div class="cardhead">
          <div>Inventory Status</div>
          <div class="muted">Low stock warnings</div>
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
                  $isLow = $stock <= $reorder;
                @endphp
                <tr>
                  <td>
                    <strong>{{ $ing->name }}</strong>
                    <div class="muted" style="margin-top:4px;">Unit: {{ $ing->unit }}</div>
                  </td>
                  <td class="{{ $isLow ? 'danger' : '' }}">{{ number_format($stock, 2) }}</td>
                  <td class="muted">{{ number_format($reorder, 2) }}</td>
                  <td>
                    @if($isLow)
                      <span class="pill low">{{ $stock <= 0 ? 'OUT' : 'LOW' }}</span>
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

        <div style="padding: 12px 14px;">
          @if($lowStockCount > 0)
            <div class="danger">⚠ Low stock detected. Please restock soon.</div>
          @else
            <div class="ok">✅ All ingredients are above reorder level.</div>
          @endif
        </div>
      </div>
    </section>

    <!-- Best Sellers -->
    <section class="card section">
      <div class="cardhead">
        <div>Best-selling Items</div>
        <div class="muted">Based on paid orders in this period</div>
      </div>

      <div style="overflow:auto;">
        <table>
          <thead>
            <tr>
              <th style="width:70%;">Item</th>
              <th>Qty Sold</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topItems as $it)
              <tr>
                <td><strong>{{ $it['name'] }}</strong></td>
                <td class="money">{{ (int)$it['qty'] }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="muted" style="text-align:center; padding: 16px;">
                  No best sellers yet (no paid orders).
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </div>
</body>
</html>