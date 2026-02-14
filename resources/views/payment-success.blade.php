<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    
      <div class="success-card">
        <div class="check-icon">✓</div>

        <h1>Payment Successful</h1>
        <p>Thank you! Your payment has been processed successfully.</p>

        <a href="/payment-receit" class="home-btn">View Receipt</a>
    </div>


    <script>
document.addEventListener('DOMContentLoaded', async () => {
  const params = new URLSearchParams(window.location.search);
  const order_code = params.get('order_code');
  const external_id = params.get('external_id');

  if (!order_code || !external_id) return;

  try {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    await fetch('/orders/mark-paid', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ order_code, external_id })
    });

    // ✅ clear cart after success
    localStorage.removeItem('cart');

    // ✅ go to track order page
    window.location.href = `/track-order?order_code=${encodeURIComponent(order_code)}`;

  } catch (e) {
    console.error(e);
  }
});
</script>

</body>
</html>