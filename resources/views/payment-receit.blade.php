<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        .receipt-card {
        max-width: 420px;
        margin: 40px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #000;
    }

    .receipt-header p {
        font-size: 14px;
        color: #555;
        margin-top: 4px;
    }

    .receipt-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px dashed #EFEFEF;
    }

    .receipt-item span:last-child {
        font-weight: 600;
    }

    .total {
        display: flex;
        justify-content: space-between;
        margin-top: 18px;
        padding-top: 12px;
        font-size: 16px;
        font-weight: 700;
        border-top: 2px solid #f7b413;
        color: #000;
    }

    .actions {
        margin-top: 25px;
        text-align: center;
    }

    .actions button {
        background: #f7b413;
        color: #000;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.2s ease;
    }

    .actions button:hover {
        background: #e6a90f;
    }
    .actions {
    margin-top: 25px;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.track-btn {
    background: #fff;
    color: #000;
    border: 2px solid #f7b413;
    padding: 12px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}

.track-btn:hover {
    background: #f7b413;
}


    /* PRINT MODE */
    @media print {
        body {
            background: #fff;
            display: flex;
            justify-content: center; 
            align-items: center;
        }

        .actions {
            display: none;
        }

        .receipt-card {
            box-shadow: none;
            border-radius: 0;
            margin: 0;
        }
    }
    </style>
</head>
<body>

<div class="receipt-card" id="receipt">
    <div class="receipt-header">
        <h2>Payment Receipt</h2>
        <p>Thank you for your purchase</p>
    </div>

    <div id="receiptItems"></div>

    <hr>

    <div class="total">
        Total: <span id="receiptTotal">₱0.00</span>
    </div>

    <div class="actions">
        <button onclick="printReceipt()">Download / Print Receipt</button>
           <button class="track-btn" onclick="trackOrder()">
        Track Order
    </button>
    </div>
  
</div>

<script>
    const itemsContainer = document.getElementById('receiptItems');
    const totalEl = document.getElementById('receiptTotal');

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let total = 0;

    if (cart.length === 0) {
        itemsContainer.innerHTML = '<p>No items found.</p>';
    } else {
        cart.forEach(item => {
            const price = Number(item.price) || 0;
            const qty = Number(item.qty) || 1;
            const subtotal = price * qty;
            total += subtotal;

            const row = document.createElement('div');
            row.className = 'receipt-item';
            row.innerHTML = `
                <span>${item.name} (${qty}x)</span>
                <span>₱${subtotal.toFixed(2)}</span>
            `;
            itemsContainer.appendChild(row);
        });
    }

    totalEl.textContent = `₱${total.toFixed(2)}`;

    function printReceipt() {
        window.print();
        localStorage.removeItem('cart');
    }
</script>

</body>
</html>
