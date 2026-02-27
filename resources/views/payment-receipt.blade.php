<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Payment Receipt</title>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

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

/* ✅ SAME STYLE AS DOWNLOAD BUTTON */
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

.action-btn:disabled{
  opacity: .75;
  cursor: not-allowed;
}

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

        <!-- ✅ DONE EATING (sets table available again) -->
        <button class="action-btn" id="doneEatingBtn" type="button">
            Done Eating
        </button>

        <!-- ✅ ORDER AGAIN BUTTON -->
        <button class="action-btn" id="orderAgainBtn" type="button">
            Order Again
        </button>

        <a href="{{ route('feedback.form') }}" style="text-decoration:none;">
            <button class="action-btn" type="button">
                Send Feedback
            </button>
        </a>

    </div>
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

<!-- ✅ Confirm Done Eating Modal -->
<div class="confirm-overlay hidden" id="doneOverlay"></div>
<div class="confirm-modal hidden" id="doneModal" role="dialog" aria-modal="true" aria-labelledby="doneTitle">
    <h3 id="doneTitle">Mark table as available?</h3>
    <p>Tap “Yes” if you are done eating. This will set your table back to available.</p>

    <div class="confirm-actions">
        <button type="button" class="confirm-cancel" id="doneCancelBtn">Cancel</button>
        <button type="button" class="confirm-ok" id="doneOkBtn">Yes, done</button>
    </div>
</div>
<!-- ✅ Success Modal -->
<div class="confirm-overlay hidden" id="successOverlay"></div>
<div class="confirm-modal hidden" id="successModal" role="dialog" aria-modal="true" aria-labelledby="successTitle">
    <h3 id="successTitle">All set!</h3>
    <p id="successMessage">Your table is now available. Thank you!</p>

    <div class="confirm-actions">
        <button type="button" class="confirm-ok" id="successOkBtn">Continue</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ✅ fade-in on load
    document.body.classList.add('page-fade-in');

    // =========================
    // ORDER AGAIN MODAL
    // =========================
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
        localStorage.removeItem('cart');
        localStorage.removeItem('order_summary');

        document.body.classList.remove('page-fade-in');
        document.body.classList.add('page-fade-out');

        setTimeout(() => {
            window.location.href = '/';
        }, 220);
    });

    // =========================
    // DONE EATING MODAL + LOGIC
    // =========================
    const doneEatingBtn = document.getElementById('doneEatingBtn');
    const doneOverlay = document.getElementById('doneOverlay');
    const doneModal = document.getElementById('doneModal');
    const doneCancel = document.getElementById('doneCancelBtn');
    const doneOk = document.getElementById('doneOkBtn');

    function openDone(){
        doneOverlay.classList.remove('hidden');
        doneModal.classList.remove('hidden');
        doneModal.classList.add('show');
    }

    function closeDone(){
        doneOverlay.classList.add('hidden');
        doneModal.classList.add('hidden');
        doneModal.classList.remove('show');
    }

    doneEatingBtn?.addEventListener('click', openDone);
    doneOverlay?.addEventListener('click', closeDone);
    doneCancel?.addEventListener('click', closeDone);

    doneOk?.addEventListener('click', async function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        doneOk.disabled = true;
        doneEatingBtn.disabled = true;
        doneOk.textContent = 'Updating...';

        try {
            const res = await fetch("{{ route('table.done_eating') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    "Accept": "application/json"
                }
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || data.ok === false) {
                alert(data.message || 'Failed to update table.');
                doneOk.disabled = false;
                doneEatingBtn.disabled = false;
                doneOk.textContent = 'Yes, done';
                return;
            }

           closeDone();

// optional: clear cart to avoid re-using old order
localStorage.removeItem('cart');
localStorage.removeItem('order_summary');

// ✅ Show success modal instead of alert
openSuccess('Your table is now available. Thank you!');

        } catch (e) {
            alert('Network error. Please try again.');
            doneOk.disabled = false;
            doneEatingBtn.disabled = false;
            doneOk.textContent = 'Yes, done';
        }
    });
// =========================
// ✅ SUCCESS MODAL
// =========================
const successOverlay = document.getElementById('successOverlay');
const successModal = document.getElementById('successModal');
const successMsg = document.getElementById('successMessage');
const successOk = document.getElementById('successOkBtn');

function openSuccess(message){
    if (successMsg) successMsg.textContent = message || 'Success!';
    successOverlay.classList.remove('hidden');
    successModal.classList.remove('hidden');
    successModal.classList.add('show');
}

function closeSuccess(){
    successOverlay.classList.add('hidden');
    successModal.classList.add('hidden');
    successModal.classList.remove('show');
}

// click to close + continue
successOverlay?.addEventListener('click', closeSuccess);

successOk?.addEventListener('click', function () {
    closeSuccess();

    // ✅ smooth fade then redirect
    document.body.classList.remove('page-fade-in');
    document.body.classList.add('page-fade-out');

    setTimeout(() => {
        window.location.href = '/';
    }, 220);
});
    // ESC closes whichever modal is open
    document.addEventListener('keydown', function(e){
       if (!modal.classList.contains('hidden')) closeConfirm();
if (!doneModal.classList.contains('hidden')) closeDone();
if (!successModal.classList.contains('hidden')) closeSuccess();
    });
});
</script>

</body>
</html>