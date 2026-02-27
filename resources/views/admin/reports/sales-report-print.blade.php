<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sales Report</title>

  <style>
    :root{
      --text: #111;
      --muted: #6b7280;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --card: #fff;
      --border: #e5e7eb;
      --bg: #f7f7f7;
      --shadow-soft: 0 10px 30px rgba(0,0,0,.08);
      --radius: 16px;
    }
    * { box-sizing: border-box; }
    body{ margin:0; font-family: Arial, sans-serif; background: var(--bg); color: var(--text); padding: 20px; }
    .sheet{ max-width: 980px; margin: 0 auto; background: var(--card); border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius); box-shadow: var(--shadow-soft); overflow: hidden; }
    .topbar{ display:flex; align-items:center; justify-content:space-between; padding: 16px 18px;
      background: linear-gradient(0deg, rgba(245,158,11,.10), rgba(245,158,11,.10));
      border-bottom: 1px solid rgba(0,0,0,.06); gap: 10px; flex-wrap: wrap; }
    .brand{ display:flex; align-items:center; gap: 10px; font-weight: 900; }
    .logo{ width: 46px; height: 46px; border-radius: 12px; background: #fff; border: 1px solid rgba(0,0,0,.08);
      display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .logo img{ width: 100%; height: 100%; object-fit: contain; padding: 6px; }
    .title-wrap h1{ margin:0; font-size: 26px; font-weight: 900; color: var(--orange); line-height: 1.1; }
    .title-wrap .sub{ margin-top: 4px; font-size: 12.5px; color: var(--muted); font-weight: 700; }
    .actions{ display:flex; gap: 10px; align-items:center; flex-wrap: wrap; }
    .btn{ padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(0,0,0,.10);
      background: #111; color:#fff; font-weight: 900; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn.secondary{ background:#fff; color:#111; }
    .content{ padding: 18px; }

    .meta-grid{ display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
    .meta-card{ background:#fff; border: 1px solid rgba(245,158,11,.45); border-radius: var(--radius);
      padding: 12px; box-shadow: 0 6px 16px rgba(0,0,0,.06); min-height: 78px; }
    .meta-card .label{ font-size: 11px; font-weight: 900; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 6px; }
    .meta-card .value{ font-size: 14px; font-weight: 900; color:#111; line-height: 1.25; }
    .meta-wide{ grid-column: 1 / -1; border-color: rgba(0,0,0,.06); }
    .muted{ color: var(--muted); font-weight: 700; }

    .summary{ display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
    .sum-card{ background:#fff; border: 1px solid rgba(245,158,11,.45); border-radius: var(--radius);
      padding: 14px; box-shadow: 0 6px 16px rgba(0,0,0,.06); min-height: 86px; }
    .sum-card .label{ font-size: 12px; color: var(--muted); font-weight: 900; margin-bottom: 8px; }
    .sum-card .big{ font-size: 20px; font-weight: 900; color:#111; }
    .money{ color: var(--orange-2); font-weight: 900; }

    .grid2{ display:grid; grid-template-columns: 1.4fr 1fr; gap: 12px; margin-bottom: 16px; }
    .panel{ background:#fff; border: 1px solid rgba(0,0,0,.06); border-radius: var(--radius);
      box-shadow: 0 6px 16px rgba(0,0,0,.06); overflow:hidden; }
    .panel-head{ padding: 12px 14px; font-weight: 900; background: #fff7ed; border-bottom: 1px solid rgba(0,0,0,.06); }
    .panel-body{ padding: 12px 14px; }

    .table-wrap{ background:#fff; border: 1px solid rgba(0,0,0,.06); border-radius: var(--radius);
      box-shadow: 0 6px 16px rgba(0,0,0,.06); overflow:hidden; }
    .table-head{ padding: 12px 14px; background:#fff; border-bottom: 1px solid rgba(0,0,0,.06);
      font-weight: 900; display:flex; justify-content:space-between; gap: 10px; flex-wrap:wrap; }

    table{ width:100%; border-collapse: collapse; }
    th, td{ padding: 10px 12px; border-bottom: 1px solid rgba(0,0,0,.06); text-align:left; font-size: 12.5px; }
    th{ font-weight: 900; background: #fff7ed; }

    .footer{ padding: 12px 18px; color: var(--muted); font-size: 11.5px; font-weight: 700;
      border-top: 1px solid rgba(0,0,0,.06); background: #fafafa; }

    @media print{
      body{ background:#fff; padding: 0; }
      .sheet{ box-shadow:none; border:none; border-radius:0; }
      .actions, .no-print{ display:none !important; }
      .meta-card, .sum-card, .panel, .table-wrap{ box-shadow:none; }
    }
    @page{ size: A4; margin: 12mm; }
    @media (max-width: 820px){ .meta-grid, .summary, .grid2{ grid-template-columns: 1fr; } }
  </style>
</head>
<body>

  <div class="sheet">
    <div class="topbar">
      <div class="brand">
        <div class="logo">
          <img src="{{ asset('images/logo-image.png') }}" alt="Logo">
        </div>
        <div class="title-wrap">
          <h1>Sales Report</h1>
          <div class="sub">99 Silog Cafe • Cashier Report</div>
        </div>
      </div>

   <div class="actions no-print">
  <button class="btn" onclick="window.print()">Print / Save as PDF</button>

  <!-- ✅ Excel (CSV) download -->
  <a class="btn" style="text-decoration:none;"
     href="{{ route('admin.sales-report.export.csv', ['period' => $period, 'date' => request('date')]) }}">
    Download Excel
  </a>

  <a class="btn secondary" href="{{ url()->previous() }}">Back</a>
</div>
    </div>

    <div class="content">
      <div class="meta-grid">
        <div class="meta-card">
          <div class="label">Period</div>
          <div class="value">{{ ucfirst($period) }}</div>
        </div>
        <div class="meta-card">
          <div class="label">Range</div>
          <div class="value">{{ $rangeLabel }}</div>
        </div>
        <div class="meta-card">
          <div class="label">Generated</div>
          <div class="value">{{ $generatedAt }}</div>
        </div>
        <div class="meta-card meta-wide">
          <div class="label">Notes</div>
          <div class="value muted">
            This report includes only <strong style="color:#111;">PAID</strong> orders (Xendit successful payments).
          </div>
        </div>
      </div>

      <div class="summary">
        <div class="sum-card">
          <div class="label">Total Sales (Paid)</div>
          <div class="big money">₱{{ number_format($totalSales, 2) }}</div>
        </div>
        <div class="sum-card">
          <div class="label">Paid Orders</div>
          <div class="big">{{ $paidCount }}</div>
        </div>
        <div class="sum-card">
          <div class="label">Average Order</div>
          <div class="big">₱{{ number_format($avgOrder, 2) }}</div>
        </div>
      </div>

      <!-- ✅ CHART + BEST SELLERS -->
      <div class="grid2">

        <!-- Chart -->
        <div class="panel">
          <div class="panel-head">Sales Graph</div>
          <div class="panel-body">
          @php
  $vals = $chartValues ?? [];
  $labs = $chartLabels ?? [];

  // keep chart readable: max 12 labels shown (auto)
  $n = count($vals);
  $max = 0;
  foreach ($vals as $v) { if ($v > $max) $max = $v; }
  $max = $max > 0 ? $max : 1;

  $w = 720;
  $h = 240;
  $padL = 44;   // left padding for Y labels
  $padR = 18;
  $padT = 18;
  $padB = 42;   // bottom padding for X labels
  $gap  = 6;

  $plotW = $w - $padL - $padR;
  $plotH = $h - $padT - $padB;

  $barW = $n > 0 ? max(6, (int)(($plotW - ($n-1)*$gap) / $n)) : 10;

  // y-axis ticks (0%, 50%, 100%)
  $tickVals = [0, $max/2, $max];
  $fmtPeso = function($num){
      // simple money formatting (no dependency)
      return '₱' . number_format((float)$num, 0);
  };

  // show only some x labels (avoid clutter)
  $maxLabels = 12;
  $step = $n > $maxLabels ? (int)ceil($n / $maxLabels) : 1;
@endphp

<svg width="100%" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none"
     style="display:block; border:1px solid rgba(0,0,0,.06); border-radius:12px; background:#fff;">

  <!-- Y grid + labels -->
  @for($t=0; $t<count($tickVals); $t++)
    @php
      $tv = (float)$tickVals[$t];
      $y = $padT + $plotH - ($plotH * ($tv / $max));
    @endphp
    <line x1="{{ $padL }}" y1="{{ $y }}" x2="{{ $w - $padR }}" y2="{{ $y }}" stroke="#eee" />
    <text x="{{ $padL - 8 }}" y="{{ $y + 4 }}" font-size="10" text-anchor="end" fill="#6b7280">
      {{ $fmtPeso($tv) }}
    </text>
  @endfor

  <!-- X axis -->
  <line x1="{{ $padL }}" y1="{{ $padT + $plotH }}" x2="{{ $w - $padR }}" y2="{{ $padT + $plotH }}" stroke="#ddd" />

  <!-- Bars + values -->
  @for($i=0; $i<$n; $i++)
    @php
      $v = (float)$vals[$i];
      $barH = (int)round($plotH * ($v / $max));
      $x = $padL + $i * ($barW + $gap);
      $y = ($padT + $plotH) - $barH;
    @endphp

    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $barH }}"
          fill="#ffb300" opacity="0.95" />

    <!-- value label (only if bar is tall enough) -->
    @if($barH > 26)
      <text x="{{ $x + $barW/2 }}" y="{{ $y + 12 }}" font-size="10" text-anchor="middle" fill="#111">
        {{ number_format($v, 0) }}
      </text>
    @endif

    <!-- X labels (skip some to avoid clutter) -->
    @if($i % $step === 0)
      <text x="{{ $x + $barW/2 }}" y="{{ $padT + $plotH + 16 }}" font-size="10"
            text-anchor="middle" fill="#6b7280">
        {{ $labs[$i] ?? '' }}
      </text>
    @endif
  @endfor

</svg>

<div class="muted" style="margin-top:8px; font-size:12px;">
  Indicator: Bars = total sales per bucket.
  @if(($period ?? 'daily') === 'daily')
    Buckets are by <strong style="color:#111;">hour</strong>.
  @elseif(($period ?? 'daily') === 'yearly')
    Buckets are by <strong style="color:#111;">month</strong>.
  @else
    Buckets are by <strong style="color:#111;">day</strong>.
  @endif
</div>
          </div>
        </div>

        <!-- Best sellers -->
        <div class="panel">
          <div class="panel-head">Best-Selling Items</div>
          <div class="panel-body">
            <table>
              <thead>
                <tr>
                  <th>Item</th>
                  <th style="width:90px;">Qty</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topItems as $it)
                  <tr>
                    <td><strong>{{ $it['name'] }}</strong></td>
                    <td class="money">{{ $it['qty'] }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="muted" style="text-align:center; padding: 14px;">
                      No items found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- Orders Table -->
      <div class="table-wrap">
        <div class="table-head">
          <div>Paid Orders List</div>
          <div class="muted">Newest first</div>
        </div>

        <div style="overflow:auto;">
          <table>
            <thead>
              <tr>
                <th style="width: 24%;">Order Code</th>
                <th style="width: 10%;">Table</th>
                <th style="width: 14%;">Total</th>
                <th style="width: 14%;">Status</th>
                <th style="width: 18%;">Paid Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($paidOrders as $order)
                <tr>
                  <td><strong>{{ $order->order_code }}</strong></td>
                  <td class="muted">{{ $order->table_number }}</td>
                  <td class="money">₱{{ number_format((float)$order->total, 2) }}</td>
                  <td class="muted">{{ ucfirst($order->status) }}</td>
                  <td class="muted">{{ optional($order->created_at)->format('Y-m-d h:i A') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="muted" style="text-align:center; padding: 18px;">
                    No paid orders found for this range.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <div class="footer">
      Generated by Resonance • Keep a copy for records.
    </div>
  </div>

</body>
</html>