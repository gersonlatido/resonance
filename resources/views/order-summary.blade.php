<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        // Render cart from localStorage on this page
        document.addEventListener('DOMContentLoaded', () => {
            const summaryItems = document.querySelector('.summary-items');
            const summaryTotal = document.querySelector('.summary-total-amount');
            const confirmBtn = document.getElementById('confirmPayment');
            const cancelBtn = document.getElementById('cancelSummary');

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length === 0) {
                summaryItems.innerHTML = '<p>Your cart is empty</p>';
                summaryTotal.textContent = '₱0.00';
                return;
            }

            let total = 0;
            summaryItems.innerHTML = '';
            cart.forEach(item => {
                total += item.price * item.qty;
                const el = document.createElement('div');
                el.className = 'summary-item';
                el.innerHTML = `
                    <div>
                        <p>${item.name}</p>
                        <small>${item.qty} × ₱${item.price.toFixed(2)}</small>
                    </div>
                    <strong>₱${(item.price * item.qty).toFixed(2)}</strong>
                `;
                summaryItems.appendChild(el);
            });
            summaryTotal.textContent = `₱${total.toFixed(2)}`;

            confirmBtn.addEventListener('click', () => {
                alert('Order placed successfully!');
                localStorage.removeItem('cart');
                window.location.href = '/';
            });

            cancelBtn.addEventListener('click', () => {
                window.history.back();
            });
        });
    </script>
</head>
<body>
    <div class="order-summary-page">
        <header>
            <h1>Order Summary</h1>
        </header>

        <main>
            <div class="summary-items"></div>
            <div class="summary-total">
                <strong>Total:</strong>
                <span class="summary-total-amount">₱0.00</span>
            </div>

            <div class="summary-actions">
                <button id="confirmPayment" class="confirm-payment-btn">Confirm Payment</button>
                <button id="cancelSummary" class="cancel-summary-btn">Cancel</button>
            </div>
        </main>
    </div>
</body>
</html>
