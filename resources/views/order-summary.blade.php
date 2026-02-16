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
<<<<<<< HEAD

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
=======
>>>>>>> parent of 5bad5b6 (Merge branch 'main' of https://github.com/gersonlatido/resonance)

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

            if (confirmBtn) {
                confirmBtn.textContent = `Pay ₱${total.toFixed(2)}`;
            }

            // ✅ MAIN FIX:
            // 1) Save cart as PENDING ORDER in DB
            // 2) Then call /payment/initiate to get PayMongo redirect
            confirmBtn.addEventListener('click', async () => {
                try {
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Processing...';

                    // --- 1) Save order to DB as pending ---
                    const saveRes = await fetch('/orders/from-cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ cart })
                    });

                    const saveType = saveRes.headers.get('content-type') || '';
                    let saveData;
                    if (saveType.includes('application/json')) {
                        saveData = await saveRes.json();
                    } else {
                        throw new Error('Server returned non-JSON while saving order.');
                    }

                    if (!saveRes.ok) {
                        throw new Error(saveData.message || 'Failed to save order.');
                    }

                    // Save pending order id so payment success can update it later
                    localStorage.setItem('pending_order_id', String(saveData.order_id));
                    localStorage.setItem('pending_order_code', String(saveData.order_code));

                    // --- 2) Initiate PayMongo payment ---
                    const res = await fetch('/payment/initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ cart })
                    });

                    const contentType = res.headers.get('content-type') || '';
                    let data;
                    if (contentType.includes('application/json')) {
                        data = await res.json();
                    } else {
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
                    console.error(err);
                    alert(err.message || 'Unable to initiate payment');
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = `Pay ₱${total.toFixed(2)}`;
                }
            });
        });
    </script>
</body>
</html>
