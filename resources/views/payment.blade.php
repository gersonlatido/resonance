{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="order-summary-page">
        <header>
            <h1>Choose Payment Method</h1>
        </header>

        <main>
            <div class="payment-items"></div>

            <div class="summary-total">
                <span>Total</span>
                <span id="paymentTotal">₱0.00</span>
            </div>

            <!-- Payment provider choice will be presented by PayMongo's hosted flow.
                 We do not collect the e-wallet type here; backend will create the
                 PayMongo source/intent and redirect to their checkout page. -->

            <div class="summary-actions">
                <!-- Payment is initiated from the Order Summary page; this page is
                     only used as a fallback/redirect target from the payment API. -->
                <p style="color:#666;">If you see this page, follow the instructions provided by the payment provider.</p>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/payment.js') }}" defer></script>
</body>
</html> --}}
