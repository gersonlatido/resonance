<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body.track-order-page {
            font-family: 'Figtree', sans-serif;
            background-color: #EFEFEF;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 16px 24px;
        }

        .track-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .track-card {
            background-color: #fff;
            width: 100%;
            max-width: 460px;
            padding: 24px 20px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.10);
        }

        .track-card h2 {
            color: #f7b413;
            margin-bottom: 12px;
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 800;
        }

        .track-card p {
            color: #555;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .track-logo {
            width: min(72vw, 280px);
            margin: 0 auto 20px;
        }

        .track-meta {
            display: grid;
            gap: 12px;
            margin-top: 8px;
        }

        .track-info-box {
            background: #fff8e6;
            border: 1px solid rgba(247, 180, 19, 0.35);
            border-radius: 14px;
            padding: 14px 12px;
        }

        .track-label {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .eta-value {
            color: #f7b413;
            font-weight: 800;
            font-size: clamp(22px, 5vw, 32px);
            letter-spacing: 1px;
        }

        .status {
            font-weight: 800;
            color: #f7b413;
            font-size: clamp(18px, 4vw, 22px);
        }

        .track-subtext {
            font-size: 13px;
            color: #6b7280;
            margin-top: 14px;
            margin-bottom: 0;
        }

        .track-back-btn {
            position: fixed;
            top: 16px;
            left: 16px;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.08);
            color: #f7b413;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            z-index: 1000;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .track-back-btn:hover {
            opacity: 0.92;
        }

        @media (max-width: 480px) {
            body.track-order-page {
                padding: 76px 12px 20px;
            }

            .track-card {
                padding: 20px 16px;
                border-radius: 16px;
            }

            .track-card p {
                font-size: 14px;
            }

            .track-back-btn {
                top: 12px;
                left: 12px;
                font-size: 14px;
                padding: 9px 12px;
            }
        }

        @media (min-width: 768px) {
            body.track-order-page {
                padding-top: 96px;
                padding-left: 24px;
                padding-right: 24px;
            }

            .track-card {
                max-width: 520px;
                padding: 30px 28px;
            }

            .track-logo {
                width: 300px;
            }
        }
    </style>
</head>
<body class="track-order-page">

    <button
        type="button"
        class="track-back-btn"
        onclick="window.location.href='{{ url('/payment-receipt') }}'">
        ← Back
    </button>

    <main class="track-wrapper">
        <section class="track-card">
            <h2>Tracking Order</h2>
            <p>Your order will be at your table soon.</p>

            <img src="{{ asset('images/logo-image.png') }}" alt="Logo" class="track-logo">

            <div class="track-meta">
                <div class="track-info-box">
                    <span class="track-label">Estimated Time Remaining</span>
                    <strong id="etaText" class="eta-value">00:00</strong>
                </div>

                <div class="track-info-box">
                    <span class="track-label">Current Status</span>
                    <span id="statusText" class="status">Preparing</span>
                </div>
            </div>

            <p class="track-subtext">
                Please stay at your table while your order is being prepared.
            </p>
        </section>
    </main>

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

        tick();
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

          const niceStatus = (order.status || 'preparing')
            .toString()
            .replace(/^\w/, c => c.toUpperCase());

          statusEl.textContent = niceStatus;

          const etaMinutes = (order.eta_minutes === null || order.eta_minutes === undefined || order.eta_minutes === '')
            ? null
            : Number(order.eta_minutes);

          if (!etaMinutes || etaMinutes <= 0) {
            etaEl.textContent = '--:--';
            localStorage.removeItem(STORAGE_KEY);

            if (window.__etaTimer) {
              clearInterval(window.__etaTimer);
              window.__etaTimer = null;
            }
            return;
          }

          let saved = null;
          try {
            saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
          } catch (e) {
            saved = null;
          }

          if (!saved || saved.etaMinutes !== etaMinutes || !saved.endEpochMs) {
            const endEpochMs = Date.now() + etaMinutes * 60 * 1000;
            localStorage.setItem(STORAGE_KEY, JSON.stringify({ etaMinutes, endEpochMs }));
            startCountdown(endEpochMs);
          } else {
            startCountdown(saved.endEpochMs);
          }

        } catch (e) {
          statusEl.textContent = 'Order not found';
          etaEl.textContent = '--:--';
        }
      }

      syncFromServer();

      if (window.__poller) clearInterval(window.__poller);
      window.__poller = setInterval(syncFromServer, 5000);
    });
    </script>

</body>
</html>