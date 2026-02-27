<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Receipt</title>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>
    <style>
/* ✅ Smooth page fade */
.page-fade-out{
  opacity: 0;
  transform: translateY(6px);
  transition: opacity .25s ease, transform .25s ease;
}
.page-fade-in{
  opacity: 1;
  transform: translateY(0);
  transition: opacity .25s ease, transform .25s ease;
}

/* ✅ Modal */
.confirm-overlay{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 9998;
}

.confirm-modal{
  position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: min(92vw, 360px);
  background: #fff;
  border-radius: 14px;
  padding: 18px 16px;
  z-index: 9999;
  box-shadow: 0 18px 40px rgba(0,0,0,.25);
}

.confirm-modal h3{
  margin: 0 0 6px;
  font-size: 18px;
  font-weight: 800;
}

.confirm-modal p{
  margin: 0 0 14px;
  font-size: 13px;
  color: #555;
  line-height: 1.4;
}

.confirm-actions{
  display: flex;
  gap: 10px;
}

.confirm-actions button{
  flex: 1;
  border: none;
  border-radius: 10px;
  padding: 11px 12px;
  font-weight: 700;
  cursor: pointer;
}

.confirm-cancel{
  background: #f3f4f6;
  color: #111;
}

.confirm-ok{
  background: #f7b413;
  color: #111;
}

.hidden{ display: none !important; }

/*  Pop animation */
.confirm-modal.show{
  animation: pop .18s ease-out;
}
@keyframes pop{
  from{ transform: translate(-50%, -50%) scale(.96); opacity: .6; }
  to{ transform: translate(-50%, -50%) scale(1); opacity: 1; }
}

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

        <button class="action-btn" onclick="window.print()">
        Download / Print Receipt
        </button>

     <a href="{{ route('track.order') }}" style="text-decoration:none;">
        <button class="action-btn" type="button">
            Track Order
        </button>
     </a>

      <!--  ORDER AGAIN BUTTON -->
     <button class="action-btn" id="orderAgainBtn" type="button">
        Order Again
        </button>

     <a href="{{ route('feedback.form') }}" style="text-decoration:none;">
        <button class="action-btn" type="button">
            Send Feedback
        </button>
     </a>

    </div>
    <!-- ✅ Confirm New Order Modal -->
<div class="confirm-overlay hidden" id="confirmOverlay"></div>

<div class="confirm-modal hidden" id="confirmModal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <h3 id="confirmTitle">Start new order?</h3>
    <p>This will clear your current cart and bring you back to the menu.</p>

    <div class="confirm-actions">
        <button type="button" class="confirm-cancel" id="confirmCancelBtn">Cancel</button>
        <button type="button" class="confirm-ok" id="confirmOkBtn">Yes, start</button>
    </div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ fade-in on load
    document.body.classList.add('page-fade-in');

    const orderAgainBtn = document.getElementById('orderAgainBtn');

    const overlay = document.getElementById('confirmOverlay');
    const modal = document.getElementById('confirmModal');

    const cancelBtn = document.getElementById('confirmCancelBtn');
    const okBtn = document.getElementById('confirmOkBtn');

    function openConfirm(){
        overlay.classList.remove('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('show');
    }

    function closeConfirm(){
        overlay.classList.add('hidden');
        modal.classList.add('hidden');
        modal.classList.remove('show');
    }

    orderAgainBtn?.addEventListener('click', openConfirm);
    overlay?.addEventListener('click', closeConfirm);
    cancelBtn?.addEventListener('click', closeConfirm);

    okBtn?.addEventListener('click', function () {
        // ✅ clear cart
        localStorage.removeItem('cart');
        localStorage.removeItem('order_summary');

        // ✅ smooth fade then redirect
        document.body.classList.remove('page-fade-in');
        document.body.classList.add('page-fade-out');

        setTimeout(() => {
            window.location.href = '/';
        }, 220);
    });

    // ESC to close
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeConfirm();
        }
    });
});
</script>
</body>
</html>