<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Payment Success</title>
  <style>
    body{font-family:Arial,sans-serif;margin:0;padding:40px;background:#fff;}
    .box{max-width:520px;margin:0 auto;border:1px solid #eee;border-radius:12px;padding:20px;}
    h1{margin:0 0 10px;}
    p{color:#444;}
  </style>
</head>
<body>
  <div class="box">
    <h1>Payment Successful ✅</h1>
    <p id="msg">Finalizing your order...</p>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      const msg = document.getElementById('msg');
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      const orderId = localStorage.getItem('pending_order_id');

      if (!orderId) {
        msg.textContent = "No pending order found. (Maybe you refreshed or cleared storage)";
        return;
      }

      try {
        // ✅ Mark order as paid in DB
        const res = await fetch('/orders/mark-paid', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({ order_id: Number(orderId) })
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || "Failed to mark order as paid");

        // ✅ Clear cart AFTER payment success is confirmed
        localStorage.removeItem('cart');
        localStorage.removeItem('pending_order_id');
        localStorage.removeItem('pending_order_code');

        msg.textContent = "Order confirmed and sent to cashier ✅";
        // optional redirect after 2 seconds:
        setTimeout(() => window.location.href = '/', 2000);

      } catch (err) {
        console.error(err);
        msg.textContent = "Payment succeeded but order update failed: " + (err.message || "Error");
      }
    });
  </script>
</body>
</html>
