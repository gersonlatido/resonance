<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <!-- initial text; will be updated by JS to show computed total -->
                <button id="confirmPayment" class="confirm-payment-btn">Pay ₱0.00</button>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const summaryItems = document.querySelector('.summary-items');
            const summaryTotal = document.querySelector('.summary-total-amount');
            const confirmBtn = document.getElementById('confirmPayment');

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (!summaryItems || !summaryTotal || !confirmBtn) return;

    if (cart.length === 0) {
        summaryItems.innerHTML = '<p>Your cart is empty</p>';
        summaryTotal.textContent = '₱0.00';
        confirmBtn.disabled = true;
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

            // Update confirm button label to include the computed total
            if (confirmBtn) {
                confirmBtn.textContent = `Pay ₱${total.toFixed(2)}`;
            }

            // When user confirms on the order-summary page, send the cart to
            // the backend to create a PayMongo source/checkout URL. The
            // backend will return a `redirect` URL (PayMongo hosted page)
            // which we navigate the browser to.
            confirmBtn.addEventListener('click', async () => {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch('/payment/initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ cart })
                    });

                    // Safely handle JSON and non-JSON responses to avoid
                    // "Unexpected token '<'" when the server returns HTML.
                    const contentType = res.headers.get('content-type') || '';
                    let data;
                    if (contentType.includes('application/json')) {
                        data = await res.json();
                    } else {
                        // server returned HTML (likely an error page) — read as text
                        const text = await res.text();
                        throw new Error('Server returned non-JSON response: ' + text);
                    }

                    if (!res.ok) throw new Error(data.error || 'Payment initiation failed');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        throw new Error('No redirect URL returned');
                    }
                } catch (err) {
                    // If server returned structured JSON with debug info, show it.
                    try {
                        const json = await err?.response?.json?.();
                        if (json && json.paymongo_body) {
                            alert('Payment error: ' + JSON.stringify(json.paymongo_body));
                            return;
                        }
                    } catch (e) {
                        // ignore
                    }
                    alert(err.message || 'Unable to initiate payment');
                }
            });
        });
    </script>
</body>
</html>
