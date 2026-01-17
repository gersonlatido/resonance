document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.querySelector('.payment-items');
    const totalEl = document.getElementById('paymentTotal');
    const startBtn = document.getElementById('startPayment');

    // Stop if not on payment page
    if (!itemsContainer || !totalEl || !startBtn) return;

    // Get cart from localStorage
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart.length === 0) {
        itemsContainer.innerHTML = '<p>Your cart is empty</p>';
        totalEl.textContent = '₱0.00';
        startBtn.disabled = true;
        return;
    }

    // Render cart
    let total = 0;
    itemsContainer.innerHTML = '';

    cart.forEach(item => {
        const price = Number(item.price) || 0;
        const qty = Number(item.qty) || 1;
        const subtotal = price * qty;

        total += subtotal;

        const row = document.createElement('div');
        row.className = 'summary-item';
        row.innerHTML = `
            <div>
                <p>${item.name}</p>
                <small>${qty} × ₱${price.toFixed(2)}</small>
            </div>
            <strong>₱${subtotal.toFixed(2)}</strong>
        `;
        itemsContainer.appendChild(row);
    });

    totalEl.textContent = `₱${total.toFixed(2)}`;
    startBtn.textContent = `Pay ₱${total.toFixed(2)}`;

    // Start payment
    startBtn.addEventListener('click', async () => {
        startBtn.disabled = true;
        startBtn.textContent = 'Processing...';

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!token) {
                throw new Error('CSRF token missing');
            }

const res = await fetch('/pay', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify(payload)
});

const text = await res.text();
console.log('Server response:', text);

let data;
try {
    data = JSON.parse(text);
} catch {
    throw new Error('Server returned HTML instead of JSON');
}

if (!res.ok) {
    throw new Error(data.error || 'Payment failed');
}

window.location.href = data.redirect;

            if (data.redirect) {
                // OPTIONAL: clear cart only after successful redirect
                // localStorage.removeItem('cart');

                window.location.href = data.redirect;
            } else {
                throw new Error('No redirect URL returned');
            }

        } catch (err) {
            alert(err.message || 'Payment error');
            startBtn.disabled = false;
            startBtn.textContent = `Pay ₱${total.toFixed(2)}`;
        }
    });
});








