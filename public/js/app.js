// ===============================
// ✅ MENU TOGGLE (CATEGORY MENU)
// ===============================
document.addEventListener('DOMContentLoaded', function () {
  const menuBtn = document.querySelector('.menu-btn');
  const categoryContainer = document.querySelector('.category-container');
  const closeBtn = document.querySelector('.category-close');

  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      categoryContainer.classList.toggle('displayed');
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      categoryContainer.classList.remove('displayed');
    });
  }
});

// ===============================
// ✅ TABLE NUMBER (QR -> session -> localStorage)
// ===============================
function syncTableNumber() {
  const serverTableEl = document.getElementById('serverTableNumber');
  const tableFromServer = serverTableEl?.dataset?.table;

  if (tableFromServer) localStorage.setItem('table_number', tableFromServer);

  const savedTable = localStorage.getItem('table_number');
  const tableNumberSpan = document.getElementById('tableNumber');
  if (tableNumberSpan) tableNumberSpan.textContent = savedTable ? savedTable : '';
}

// ===============================
// 🛒 CART SYSTEM (LOCALSTORAGE) + MENU RENDER
// ===============================
document.addEventListener('DOMContentLoaded', () => {
  syncTableNumber();

  // ===============================
  // ✅ MENU DATA (FROM API) - WITH STOCK FLAGS
  // ===============================
  let menuItems = [];

  function normalizeCategory(str) {
    return String(str || '')
      .toLowerCase()
      .replace(/&/g, 'and')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function resolveImagePath(img) {
    const s = String(img || '').trim();
    if (!s) return '';
    if (s.startsWith('http')) return s;
    if (s.startsWith('/')) return s;
    return `/images/${s}`;
  }

  function isDisabledByStock(product) {
    // IMPORTANT:
    // disable if cannot make OR low stock
    const canMake = Boolean(product.can_make);
    const lowStock = Boolean(product.low_stock);
    return (!canMake || lowStock);
  }

  function buttonLabel(product) {
    if (!product.can_make) return 'Out of stock';
    if (product.low_stock) return 'Low stock';
    return '+ Add';
  }

  function displayProducts(categoryKey, containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const key = normalizeCategory(categoryKey);
    const filtered = menuItems.filter(item => normalizeCategory(item.category) === key);

    container.innerHTML = filtered.map(product => {
      const disabled = isDisabledByStock(product);
      const label = buttonLabel(product);

      return `
        <div class="menu-item">
          <div class="menu-item-image">
            <img src="${resolveImagePath(product.image)}" alt="${product.name || ''}">
          </div>
          <div class="menu-item-details">
            <h3 class="menu-item-name">${product.name || ''}</h3>
            <p class="menu-item-description">${product.description || ''}</p>

            <div class="menu-item-button">
              <span class="menu-item-price">₱ ${Number(product.price || 0).toFixed(2)}</span>

              <button
                class="add-to-cart-btn ${disabled ? 'stock-disabled' : ''}"
                data-id="${product.menu_id}"
                ${disabled ? 'disabled' : ''}
                style="
                  ${disabled ? 'pointer-events:none;' : ''}
                  opacity:${disabled ? 0.6 : 1};
                  cursor:${disabled ? 'not-allowed' : 'pointer'};
                "
              >
                ${label}
              </button>
            </div>

          </div>
        </div>
      `;
    }).join('');

    // ✅ Attach click (ONLY for enabled buttons)
    container.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        // HARD BLOCK
        if (this.disabled || this.getAttribute('disabled') !== null) return;

        const productId = this.dataset.id;
        const productToAdd = menuItems.find(p => String(p.menu_id) === String(productId));
        if (!productToAdd) return;

        // Extra hard check (even if DOM is wrong)
        if (isDisabledByStock(productToAdd)) return;

        addToCart(productToAdd);

        // "Added" animation ONLY if not stock-disabled
        if (this.classList.contains('added')) return;

        const originalText = this.textContent;
        this.textContent = 'Added';
        this.classList.add('added');
        this.disabled = true;

        setTimeout(() => {
          // ✅ DO NOT re-enable if the menu is low/out-of-stock
          if (isDisabledByStock(productToAdd)) {
            this.textContent = buttonLabel(productToAdd);
            this.classList.remove('added');
            this.disabled = true;
            return;
          }

          this.textContent = originalText;
          this.classList.remove('added');
          this.disabled = false;
        }, 1200);
      });
    });
  }

  function renderAllCategories() {
    const categories = {
      "all-day-breakfast": ".all-day-breakfast-container",
      "main-courses": ".main-courses-container",
      "pasta": ".pasta-container",
      "unlimited-premium": ".unlimited-premium-container",
      "chicken-wings": ".chicken-wings-container",
      "chicken-chops": ".chicken-chops-container",
      "overload-premium": ".overload-container",
      "solo-mini": ".solo-mini-container",
      "frappuccino": ".frappuccino-container",
      "coffee-based": ".coffee-based-container",
      "milk-based": ".milk-based-container",
      "snacks": ".snacks-container"
    };

    for (const [cat, selector] of Object.entries(categories)) {
      displayProducts(cat, selector);
    }
  }

  // ✅ Fetch from API (must include can_make and low_stock)
  fetch('/api/menu', { cache: 'no-store' })
    .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch menu'))
    .then(data => {
      menuItems = Array.isArray(data) ? data : [];
      renderAllCategories();
    })
    .catch(err => console.error('Menu API error:', err));

  // ===============================
  // CART UI
  // ===============================
  const cartBtn = document.querySelector('.cart-btn');
  const cartContainer = document.querySelector('.cart-container');
  const cartOverlay = document.querySelector('.cart-overlay');
  const cartClose = document.querySelector('.cart-close');
  const cartItemsContainer = document.querySelector('.cart-items');
  const cartTotalEl = document.querySelector('.cart-total');
  const proceedBtns = document.querySelectorAll('.checkout-btn');

  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
  }

  function openCart() {
    cartContainer?.classList.add('active');
    cartOverlay?.classList.remove('hidden');
  }

  function closeCart() {
    cartContainer?.classList.remove('active');
    cartOverlay?.classList.add('hidden');
  }

  cartBtn?.addEventListener('click', openCart);
  cartClose?.addEventListener('click', closeCart);
  cartOverlay?.addEventListener('click', closeCart);

  function renderCart() {
    cartItemsContainer.innerHTML = '';

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = '<p>Your cart is empty</p>';
      cartTotalEl.textContent = '₱0.00';
      saveCart();
      return;
    }

    let total = 0;

    cart.forEach((item, i) => {
      total += item.price * item.qty;

      cartItemsContainer.innerHTML += `
        <div class="cart-item">
          <div class="cart-item-image"><img src="${item.image}" alt="${item.name}"></div>
          <div class="cart-item-details">
            <h4>${item.name}</h4>
            <p>₱${Number(item.price).toFixed(2)}</p>
            <div class="quantity-controls">
              <button class="qty-btn minus" data-index="${i}">−</button>
              <span>${item.qty}</span>
              <button class="qty-btn plus" data-index="${i}">+</button>
            </div>
          </div>
          <button class="remove-item" data-index="${i}">🗑</button>
        </div>
      `;
    });

    cartTotalEl.textContent = `₱${total.toFixed(2)}`;
    saveCart();
  }

  cartItemsContainer.addEventListener('click', e => {
    const btn = e.target.closest('.qty-btn, .remove-item');
    if (!btn) return;

    const i = Number(btn.dataset.index);
    if (Number.isNaN(i)) return;

    if (btn.classList.contains('plus')) cart[i].qty++;
    if (btn.classList.contains('minus')) {
      cart[i].qty--;
      if (cart[i].qty <= 0) cart.splice(i, 1);
    }
    if (btn.classList.contains('remove-item')) cart.splice(i, 1);

    renderCart();
  });

  // Expose addToCart globally (your code uses window.addToCart)
  window.addToCart = (item) => {
    const existing = cart.find(c => String(c.id) === String(item.menu_id));
    if (existing) existing.qty++;
    else cart.push({
      id: item.menu_id,
      name: item.name,
      price: parseFloat(item.price),
      image: resolveImagePath(item.image),
      qty: 1
    });

    renderCart();
  };

  renderCart();

  // ===============================
  // PROCEED TO PAYMENT
  // ===============================
  proceedBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (cart.length === 0) return alert('Your cart is empty');
      window.location.href = '/order-summary';
    });
  });

  const summaryOverlay = document.querySelector('.order-summary-overlay');
  const closeSummaryBtn = document.querySelector('.close-summary-btn');
  const confirmPaymentBtn = document.querySelector('.confirm-payment-btn');

  function closeSummary() {
    const overlay = document.querySelector('.order-summary-overlay');
    const box = document.querySelector('.order-summary');
    overlay?.classList.add('hidden');
    box?.classList.add('hidden');
  }

  closeSummaryBtn?.addEventListener('click', closeSummary);
  summaryOverlay?.addEventListener('click', closeSummary);

  confirmPaymentBtn?.addEventListener('click', () => {
    alert('Order placed successfully!');
    cart = [];
    localStorage.removeItem('cart');
    renderCart();
    closeSummary();
  });
});