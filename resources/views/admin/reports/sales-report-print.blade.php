<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sales Report</title>

  <style>
    :root{
      --text: #111827;
      --muted: #6b7280;
      --muted-2: #94a3b8;
      --orange: #f59e0b;
      --orange-2: #ffb300;
      --orange-3: #d97706;
      --card: #ffffff;
      --border: #e5e7eb;
      --bg: #f8fafc;
      --soft: #fff7ed;
      --shadow-soft: 0 10px 30px rgba(0,0,0,.08);
      --radius: 18px;
    }

    * { box-sizing: border-box; }

    body{
      margin:0;
      font-family: Arial, sans-serif;
      background: linear-gradient(180deg, #fafafa, #f8fafc);
      color: var(--text);
      padding: 20px;
    }

    .sheet{
      max-width: 1100px;
      margin: 0 auto;
      background: var(--card);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
      overflow: hidden;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 18px 20px;
      background: linear-gradient(180deg, rgba(245,158,11,.16), rgba(245,158,11,.06));
      border-bottom: 1px solid rgba(0,0,0,.06);
      gap: 10px;
      flex-wrap: wrap;
    }

    .brand{
      display:flex;
      align-items:center;
      gap: 12px;
      font-weight: 900;
    }

    .logo{
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: #fff;
      border: 1px solid rgba(0,0,0,.08);
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,.06);
    }

    .logo img{
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 6px;
    }

    .title-wrap h1{
      margin:0;
      font-size: 28px;
      font-weight: 900;
      color: var(--orange);
      line-height: 1.05;
      letter-spacing: .2px;
    }

    .title-wrap .sub{
      margin-top: 5px;
      font-size: 12.5px;
      color: var(--muted);
      font-weight: 700;
    }

    .actions{
      display:flex;
      gap: 10px;
      align-items:center;
      flex-wrap: wrap;
    }

    .btn{
      padding: 10px 14px;
      border-radius: 12px;
      border: 1px solid rgba(0,0,0,.10);
      background: #111;
      color:#fff;
      font-weight: 900;
      cursor:pointer;
      text-decoration:none;
      display:inline-block;
      box-shadow: 0 6px 14px rgba(0,0,0,.08);
    }

    .btn.secondary{
      background:#fff;
      color:#111;
    }

    .content{
      padding: 20px;
    }

    .meta-grid{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 14px;
    }

    .meta-card{
      background:#fff;
      border: 1px solid rgba(245,158,11,.35);
      border-radius: var(--radius);
      padding: 14px;
      box-shadow: 0 6px 16px rgba(0,0,0,.05);
      min-height: 84px;
    }

    .meta-card .label{
      font-size: 11px;
      font-weight: 900;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-bottom: 8px;
    }

    .meta-card .value{
      font-size: 14px;
      font-weight: 900;
      color:#111;
      line-height: 1.3;
    }

    .meta-wide{
      grid-column: 1 / -1;
      border-color: rgba(0,0,0,.06);
    }

    .muted{
      color: var(--muted);
      font-weight: 700;
    }

    .summary{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }

    .sum-card{
      background:#fff;
      border: 1px solid rgba(245,158,11,.35);
      border-radius: var(--radius);
      padding: 16px;
      box-shadow: 0 6px 16px rgba(0,0,0,.05);
      min-height: 92px;
    }

    .sum-card .label{
      font-size: 12px;
      color: var(--muted);
      font-weight: 900;
      margin-bottom: 10px;
    }

    .sum-card .big{
      font-size: 20px;
      font-weight: 900;
      color:#111;
      line-height: 1;
    }

    .money{
      color: var(--orange-2);
      font-weight: 900;
    }

    .grid2{
      display:grid;
      grid-template-columns: 1.6fr .95fr;
      gap: 14px;
      margin-bottom: 16px;
    }

    .panel{
      background:#fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: 0 6px 16px rgba(0,0,0,.05);
      overflow:hidden;
    }

    .panel-head{
      padding: 13px 16px;
      font-weight: 900;
      background: var(--soft);
      border-bottom: 1px solid rgba(0,0,0,.06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }

    .panel-head small{
      color: var(--muted);
      font-weight: 800;
      font-size: 11.5px;
    }

    .panel-body{
      padding: 14px 16px;
    }

    .chart-wrap{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 18px;
      background:
        radial-gradient(circle at top left, rgba(245,158,11,.10), transparent 26%),
        linear-gradient(180deg, #ffffff, #fffbf5);
      overflow: hidden;
      padding: 12px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.5);
    }

    .chart-note{
      margin-top: 12px;
      font-size: 12px;
      color: var(--muted);
      font-weight: 700;
      line-height: 1.5;
    }

    .mini-stat-row{
      display:grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap:8px;
      margin-top:12px;
    }

    .mini-stat{
      border:1px solid rgba(0,0,0,.06);
      border-radius: 12px;
      padding:10px 11px;
      background:#fff;
    }

    .mini-stat .k{
      font-size:10px;
      color:var(--muted);
      text-transform:uppercase;
      font-weight:900;
      letter-spacing:.4px;
      margin-bottom:4px;
    }

    .mini-stat .v{
      font-size:12px;
      font-weight:900;
      color:#111;
    }

    .table-wrap{
      background:#fff;
      border: 1px solid rgba(0,0,0,.06);
      border-radius: var(--radius);
      box-shadow: 0 6px 16px rgba(0,0,0,.05);
      overflow:hidden;
    }

    .table-head{
      padding: 13px 16px;
      background:#fff;
      border-bottom: 1px solid rgba(0,0,0,.06);
      font-weight: 900;
      display:flex;
      justify-content:space-between;
      gap: 10px;
      flex-wrap:wrap;
    }

    table{
      width:100%;
      border-collapse: collapse;
    }

    th, td{
      padding: 11px 12px;
      border-bottom: 1px solid rgba(0,0,0,.06);
      text-align:left;
      font-size: 12.5px;
    }

    th{
      font-weight: 900;
      background: #fff7ed;
    }

    .footer{
      padding: 12px 18px;
      color: var(--muted);
      font-size: 11.5px;
      font-weight: 700;
      border-top: 1px solid rgba(0,0,0,.06);
      background: #fafafa;
    }

    @media print{
      body{ background:#fff; padding: 0; }
      .sheet{ box-shadow:none; border:none; border-radius:0; max-width:none; }
      .actions, .no-print{ display:none !important; }
      .meta-card, .sum-card, .panel, .table-wrap{ box-shadow:none; }
      .chart-wrap{ background:#fff; }
    }

    @page{ size: A4; margin: 12mm; }

    @media (max-width: 900px){
      .meta-grid, .summary, .grid2{ grid-template-columns: 1fr; }
      .mini-stat-row{ grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  @php
    $currentPeriod = strtolower($period ?? 'daily');

    if (($filterMode ?? 'period') === 'range') {
        $bucketTerm = 'day';
        $bucketDescription = 'day in the selected date range';
    } elseif ($currentPeriod === 'daily') {
        $bucketTerm = 'hour';
        $bucketDescription = 'hour using 12-hour clock format from 10 AM to 10 PM';
    } elseif ($currentPeriod === 'weekly') {
        $bucketTerm = 'day';
        $bucketDescription = 'day of the week (Monday to Sunday)';
    } elseif ($currentPeriod === 'monthly') {
        $bucketTerm = 'day';
        $bucketDescription = 'calendar day of the month';
    } elseif ($currentPeriod === 'yearly') {
        $bucketTerm = 'month';
        $bucketDescription = 'month of the year';
    } else {
        $bucketTerm = 'bucket';
        $bucketDescription = 'selected grouping';
    }

    $vals = $chartValues ?? [];
    $labs = $chartLabels ?? [];

    $n = count($vals);
    $max = 0;
    $sum = 0;

    foreach ($vals as $v) {
        $fv = (float) $v;
        $sum += $fv;
        if ($fv > $max) $max = $fv;
    }

    $max = $max > 0 ? $max : 1;

    $peakIndex = 0;
    foreach ($vals as $idx => $v) {
        if ((float) $v === $max) {
            $peakIndex = $idx;
            break;
        }
    }

    $peakLabel = $labs[$peakIndex] ?? '-';
    $avgBucket = $n > 0 ? ($sum / $n) : 0;

    $w = 820;
    $h = 320;
    $padL = 62;
    $padR = 18;
    $padT = 24;
    $padB = 58;
    $gap  = $n > 18 ? 4 : 8;

    $plotW = $w - $padL - $padR;
    $plotH = $h - $padT - $padB;

    $barW = $n > 0 ? max(8, (int)(($plotW - max(0, ($n - 1) * $gap)) / max(1, $n))) : 10;

    $tickVals = [0, $max * 0.5, $max];

    $fmtPeso = function($num) {
        return '₱' . number_format((float)$num, 0);
    };

    if ($currentPeriod === 'daily') {
        $maxLabels = 7;
    } elseif ($currentPeriod === 'monthly') {
        $maxLabels = 10;
    } elseif ($currentPeriod === 'yearly') {
        $maxLabels = 12;
    } else {
        $maxLabels = 7;
    }

    if (($filterMode ?? 'period') === 'range') {
        $maxLabels = 10;
    }

    $step = $n > $maxLabels ? (int) ceil($n / $maxLabels) : 1;

    $labelMinOffset = 18;
    $labelBaseOffset = 8;
    $labelSpread = 28;
  @endphp

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

        <a class="btn"
           style="text-decoration:none;"
           href="{{ route('admin.sales-report.export.csv', [
             'period' => $period,
             'date' => request('date'),
             'from' => request('from'),
             'to' => request('to')
           ]) }}">
          Download Excel
        </a>

        <a class="btn secondary" href="{{ url()->previous() }}">Back</a>
      </div>
    </div>

    <div class="content">
      <div class="meta-grid">
        <div class="meta-card">
          <div class="label">Period</div>
          <div class="value">
            @if(($filterMode ?? 'period') === 'range')
              Custom Range
            @else
              {{ ucfirst($period) }}
            @endif
          </div>
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

      <div class="grid2">

        <div class="panel">
          <div class="panel-head">
            <span>Sales Graph</span>
            <small>Grouped by {{ $bucketTerm }}</small>
          </div>

          <div class="panel-body">
            <div class="chart-wrap">
              <svg width="100%" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none"
                   style="display:block; border-radius:12px; background:#fff;">

                <defs>
                  <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#fbbf24" />
                    <stop offset="100%" stop-color="#f59e0b" />
                  </linearGradient>

                  <linearGradient id="peakBarGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#f59e0b" />
                    <stop offset="100%" stop-color="#d97706" />
                  </linearGradient>

                  <filter id="softShadow" x="-20%" y="-20%" width="140%" height="160%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000000" flood-opacity=".12" />
                  </filter>
                </defs>

                @for($t = 0; $t < count($tickVals); $t++)
                  @php
                    $tv = (float) $tickVals[$t];
                    $y = $padT + $plotH - ($plotH * ($tv / $max));
                  @endphp
                  <line x1="{{ $padL }}" y1="{{ $y }}" x2="{{ $w - $padR }}" y2="{{ $y }}" stroke="#ececec" />
                  <text x="{{ $padL - 8 }}" y="{{ $y + 4 }}" font-size="10" text-anchor="end" fill="#64748b">
                    {{ $fmtPeso($tv) }}
                  </text>
                @endfor

                <line x1="{{ $padL }}" y1="{{ $padT + $plotH }}" x2="{{ $w - $padR }}" y2="{{ $padT + $plotH }}" stroke="#d9d9d9" />

                @for($i = 0; $i < $n; $i++)
                  @php
                    $v = (float) $vals[$i];
                    $barH = (int) round($plotH * ($v / $max));
                    $displayBarH = $v > 0 ? max($barH, 4) : 0;
                    $x = $padL + $i * ($barW + $gap);
                    $y = ($padT + $plotH) - $displayBarH;
                    $isPeak = $i === $peakIndex && $v > 0;
                    $labelY = max($padT + 10, $y - $labelBaseOffset - (($i % 2) * $labelSpread));
                  @endphp

                  @if($v > 0)
                    @if($isPeak)
                      <rect x="{{ $x - 2 }}"
                            y="{{ max($padT, $y - 3) }}"
                            width="{{ $barW + 4 }}"
                            height="{{ $displayBarH + 4 }}"
                            rx="6"
                            fill="rgba(245,158,11,.16)" />
                    @endif

                    <rect x="{{ $x }}"
                          y="{{ $y }}"
                          width="{{ $barW }}"
                          height="{{ $displayBarH }}"
                          rx="5"
                          fill="{{ $isPeak ? 'url(#peakBarGradient)' : 'url(#barGradient)' }}"
                          filter="url(#softShadow)"
                          opacity="{{ $isPeak ? '1' : '.96' }}" />

                    <text x="{{ $x + $barW / 2 }}"
                          y="{{ $labelY }}"
                          font-size="10"
                          text-anchor="middle"
                          fill="#111827"
                          font-weight="700">
                      {{ number_format($v, 0) }}
                    </text>

                    <line x1="{{ $x + $barW / 2 }}"
                          y1="{{ $labelY + 4 }}"
                          x2="{{ $x + $barW / 2 }}"
                          y2="{{ $y - 2 }}"
                          stroke="rgba(17,24,39,.18)"
                          stroke-width="1" />
                  @endif

                  @if($i % $step === 0)
                    <text x="{{ $x + $barW / 2 }}"
                          y="{{ $padT + $plotH + 20 }}"
                          font-size="10"
                          text-anchor="middle"
                          fill="#64748b">
                      {{ $labs[$i] ?? '' }}
                    </text>
                  @endif
                @endfor
              </svg>
            </div>

            <div class="mini-stat-row">
              <div class="mini-stat">
                <div class="k">
                  @if($currentPeriod === 'daily')
                    Busiest Hour
                  @elseif($currentPeriod === 'weekly')
                    Busiest Day
                  @elseif($currentPeriod === 'monthly')
                    Strongest Day
                  @elseif($currentPeriod === 'yearly')
                    Best Month
                  @else
                    Highest Bucket
                  @endif
                </div>
                <div class="v">{{ $peakLabel }}</div>
              </div>

              <div class="mini-stat">
                <div class="k">Highest Sales</div>
                <div class="v">₱{{ number_format($max, 2) }}</div>
              </div>

              <div class="mini-stat">
                <div class="k">
                  @if($currentPeriod === 'daily')
                    Average Hourly Sales
                  @elseif($currentPeriod === 'weekly')
                    Average Daily Sales
                  @elseif($currentPeriod === 'monthly')
                    Average Daily Sales
                  @elseif($currentPeriod === 'yearly')
                    Average Monthly Sales
                  @else
                    Average per Bucket
                  @endif
                </div>
                <div class="v">₱{{ number_format($avgBucket, 2) }}</div>
              </div>
            </div>

            <div class="chart-note">
              Indicator: Each bar represents total sales per <strong style="color:#111;">{{ $bucketTerm }}</strong>. Data is grouped by <strong style="color:#111;">{{ $bucketDescription }}</strong>.
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <span>Best-Selling Items</span>
            <small>Top quantities sold</small>
          </div>

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