<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .order-summary-page {
            width: 100%;
            max-width: 400px;
            margin: 0 auto; /* Removed the top margin */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
            margin-top: 0; /* Ensures no extra space at the top */
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.5rem;
            color: #333;
        }

        /* Back button style */
        .back-btn {
            font-size: 1.2rem;
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #F7B413;
            margin-bottom: 20px;
            display: inline-block;
        }

        .summary-items {
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            font-size: 1rem;
        }

        .summary-item p {
            margin: 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .summary-actions {
            display: flex;
            justify-content: space-between;
        }

        .confirm-payment-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background-color: #F7B413;
            color: white;
        }

        .confirm-payment-btn:hover {
            background-color: #e59a0b;
        }
    </style>
</head>

<body>
    <div class="order-summary-page">
        <!-- Back Button -->
        <button class="back-btn" onclick="window.history.back();">← Back</button>

        <header>
            <h1>Order Summary</h1>
        </header>

        <main>
            <!-- Dynamically filled summary items -->
            <div class="summary-items"></div>

            <!-- Total Section -->
            <div class="summary-total">
                <span>Total</span>
                <span class="summary-total-amount">₱0.00</span>
            </div>

            <!-- Action Buttons -->
            <div class="summary-actions">
                <button id="confirmPayment" class="confirm-payment-btn">Confirm Payment</button>
            </div>
        </main>
    </div>

    <script>
        // Keep your existing JavaScript logic here as-is

        document.addEventListener('DOMContentLoaded', () => {
            const summaryItems = document.querySelector('.summary-items');
            const summaryTotal = document.querySelector('.summary-total-amount');
            const confirmBtn = document.getElementById('confirmPayment');

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
        });
    </script>
</body>
</html>
