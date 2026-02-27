<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sales Report</title>
  <style>
    body { font-family: Arial, sans-serif; }
    .title {
      font-size: 18px;
      font-weight: 900;
      color: #111;
      margin-bottom: 6px;
    }
    .subtitle {
      font-size: 12px;
      color: #555;
      margin-bottom: 14px;
    }
    .meta td {
      padding: 6px 8px;
      font-size: 12px;
    }
    .meta .label { font-weight: 900; color: #333; width: 160px; }
    .meta .value { color: #111; }

    .summary {
      margin: 10px 0 16px;
      border-collapse: collapse;
      width: 100%;
    }
    .summary td {
      border: 1px solid #ddd;
      padding: 10px;
      font-size: 12px;
    }
    .summary .head {
      background: #f59e0b;
      color: #111;
      font-weight: 900;
      text-align: center;
    }
    .summary .money { font-weight: 900; color: #d97706; }

    table.report {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #ddd;
    }
    table.report th, table.report td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 12px;
      text-align: left;
    }
    table.report th {
      background: #ffedd5; /* light orange */
      font-weight: 900;
    }
    .muted { color: #666; }
    .money { font-weight: 900; color: #d97706; }
  </style>
</head>
<body>

  <div class="title">99 Silog Cafe — Sales Report</div>
  <div class="subtitle">Paid orders only (Xendit success). Styled Excel export.</div>

  <table class="meta">
    <tr>
      <td class="label">Period</td>
      <td class="value">{{ ucfirst($period) }}</td>
    </tr>
    <tr>
      <td class="label">Range</td>
      <td class="value">{{ $rangeLabel }}</td>
    </tr>
    <tr>
      <td class="label">Generated</td>
      <td class="value">{{ $generatedAt }}</td>
    </tr>
  </table>

  <table class="summary">
    <tr>
      <td class="head">Total Sales (Paid)</td>
      <td class="head">Paid Orders</td>
      <td class="head">Average Order</td>
    </tr>
    <tr>
      <td class="money">₱{{ number_format($totalSales, 2) }}</td>
      <td><strong>{{ $paidCount }}</strong></td>
      <td>₱{{ number_format($avgOrder, 2) }}</td>
    </tr>
  </table>

  <table class="report">
    <thead>
      <tr>
        <th style="width: 170px;">Order Code</th>
        <th style="width: 90px;">Table</th>
        <th style="width: 120px;">Total</th>
        <th style="width: 120px;">Status</th>
        <th style="width: 180px;">Paid Time</th>
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
          <td colspan="5" class="muted" style="text-align:center; padding: 14px;">
            No paid orders found.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

</body>
</html>