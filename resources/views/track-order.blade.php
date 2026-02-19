<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #EFEFEF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .track-card {
            background-color: #fff;
            max-width: 400px;
            width: 90%;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .track-card h2 {
            color:  #f7b413;
            margin-bottom: 15px;
        }

        .track-card p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .track-card img {
            width: 350px;
            margin-bottom: 20px;
        }

        .status {
            font-weight: bold;
            color: #f7b413;
            font-size: 18px;
        }
    </style>
</head>
<body>




   <button class="back-icon" onclick="window.location.href='{{ url('/payment-receit') }}'">
    ← Back
</button>

<div class="track-card">
    <h2>Tracking Order</h2>
    <p>Your order will be at your table in <strong id="etaText" style=" color:#f7b413">00:00</strong></p>

    <img src="{{ asset('images/logo-image.png') }}" alt="Logo">
    <p>Status: <span id="statusText" class="status">Preparing</span></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const etaEl = document.getElementById('etaText');
  const statusEl = document.getElementById('statusText');

const orderCode = @json($order->order_code ?? session('order_code'));


  if (!orderCode) {
    statusEl.textContent = 'Order not found';
    etaEl.textContent = '--:--';
    return;
  }

  const STORAGE_KEY = `eta_end_${orderCode}`;

  function formatMMSS(totalSeconds) {
    totalSeconds = Math.max(0, Math.floor(Number(totalSeconds) || 0));
    const m = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
    const s = String(totalSeconds % 60).padStart(2, '0');
    return `${m}:${s}`;
  }

  function startCountdown(endEpochMs) {
    if (window.__etaTimer) clearInterval(window.__etaTimer);

    const tick = () => {
      const remainingSeconds = Math.max(0, Math.floor((endEpochMs - Date.now()) / 1000));
      etaEl.textContent = formatMMSS(remainingSeconds);
      if (remainingSeconds <= 0 && window.__etaTimer) {
        clearInterval(window.__etaTimer);
        window.__etaTimer = null;
      }
    };

    tick(); // show immediately
    window.__etaTimer = setInterval(tick, 1000);
  }

  async function fetchOrder() {
    const res = await fetch(`/api/orders/${encodeURIComponent(orderCode)}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (!res.ok) throw new Error('Order not found');
    return res.json();
  }

  async function syncFromServer() {
    try {
      const order = await fetchOrder();

      // Status text
      const niceStatus = (order.status || 'preparing')
        .toString()
        .replace(/^\w/, c => c.toUpperCase());
      statusEl.textContent = niceStatus;

      // ETA minutes (from DB)
      const etaMinutes = (order.eta_minutes === null || order.eta_minutes === undefined || order.eta_minutes === '')
        ? null
        : Number(order.eta_minutes);

      // If no ETA yet
      if (!etaMinutes || etaMinutes <= 0) {
        etaEl.textContent = '--:--';
        localStorage.removeItem(STORAGE_KEY);
        if (window.__etaTimer) {
          clearInterval(window.__etaTimer);
          window.__etaTimer = null;
        }
        return;
      }

      // Saved countdown state
      let saved = null;
      try {
        saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
      } catch (e) {
        saved = null;
      }

      // If first time OR admin changed eta_minutes → reset end time
      if (!saved || saved.etaMinutes !== etaMinutes || !saved.endEpochMs) {
        const endEpochMs = Date.now() + etaMinutes * 60 * 1000;
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ etaMinutes, endEpochMs }));
        startCountdown(endEpochMs);
      } else {
        // Keep existing end time (prevents jumping back)
        startCountdown(saved.endEpochMs);
      }

    } catch (e) {
      statusEl.textContent = 'Order not found';
      etaEl.textContent = '--:--';
    }
  }

  // Initial load
  syncFromServer();

  // Poll server every 5 seconds (NO page refresh)
  if (window.__poller) clearInterval(window.__poller);
  window.__poller = setInterval(syncFromServer, 5000);
});
</script>

</body>
</html>
