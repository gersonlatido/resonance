<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Receipt</title>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>
.receipt-card{
    max-width:420px;
    margin:40px auto;
    padding:25px;
    background:#fff;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}
.receipt-header{text-align:center;margin-bottom:20px}
.receipt-header h2{margin:0;font-size:22px;font-weight:700;color:#000}
.receipt-header p{font-size:14px;color:#555;margin-top:4px}

.receipt-item{
    display:flex;
    justify-content:space-between;
    padding:8px 0;
    font-size:14px;
    border-bottom:1px dashed #EFEFEF;
}
.receipt-item span:last-child{font-weight:600}

.total{
    display:flex;
    justify-content:space-between;
    margin-top:18px;
    padding-top:12px;
    font-size:16px;
    font-weight:700;
    border-top:2px solid #f7b413;
    color:#000;
}

.actions{
    margin-top:25px;
    text-align:center;
    display:flex;
    flex-direction:column;
    gap:10px;
}

/* ✅ SAME STYLE AS DOWNLOAD BUTTON (OLD CSS FEEL) */
.action-btn{
    background:#f7b413;
    color:#000;
    border:none;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    font-weight:600;
    transition:background 0.2s ease;
    width:100%;
}
.action-btn:hover{ background:#e6a90f; }

/* PRINT MODE */
@media print{
    body{
        background:#fff;
        display:flex;
        justify-content:center;
        align-items:center;
    }
    .actions{ display:none; }
    .receipt-card{
        box-shadow:none;
        border-radius:0;
        margin:0;
    }
}
</style>
</head>

<body>

@php
    session([
        'order_code'   => (string) ($order->order_code ?? session('order_code')),
        'table_number' => (int) ($order->table_number ?? session('table_number')),
    ]);

    $items = $order->items ?? collect();
@endphp

<div class="receipt-card" id="receipt">
    <div class="receipt-header">
        <h2>Payment Receipt</h2>
        <p>Thank you for your purchase</p>
        <p style="font-size:13px;color:#777;margin-top:8px;">
            Order Code: <strong>{{ $order->order_code }}</strong>
        </p>
    </div>

    {{-- ITEMS --}}
    @if($items->count() === 0)
        <p>No items found.</p>
    @else
        @foreach($items as $it)
            @php
                $qty = (int) ($it->qty ?? 1);
                $price = (float) ($it->price ?? 0);
                $subtotal = (float) ($it->subtotal ?? ($price * $qty));
            @endphp

            <div class="receipt-item">
                <span>{{ $it->name }} ({{ $qty }}x)</span>
                <span>₱{{ number_format($subtotal,2) }}</span>
            </div>
        @endforeach
    @endif

    <div class="total">
        <span>Total:</span>
        <span>₱{{ number_format((float)($order->total ?? 0),2) }}</span>
    </div>

    <div class="actions">
        <button class="action-btn" onclick="window.print()">Download / Print Receipt</button>

        <a href="{{ route('track.order') }}" style="text-decoration:none;">
            <button class="action-btn" type="button">Track Order</button>
        </a>

        <a href="{{ route('feedback.form') }}" style="text-decoration:none;">
            <button class="action-btn" type="button">Send Feedback</button>
        </a>
    </div>
</div>

</body>
</html>