// ===============================
// ✅ MENU TOGGLE (CATEGORY MENU)
// ===============================
document.addEventListener('DOMContentLoaded', function () {
  const menuBtn = document.querySelector('.menu-btn');
  const categoryContainer = document.querySelector('.category-container');
  const closeBtn = document.querySelector('.category-close');

  let categoryOverlay = document.querySelector('.category-overlay');
  if (!categoryOverlay) {
    categoryOverlay = document.createElement('div');
    categoryOverlay.className = 'category-overlay hidden';
    document.body.appendChild(categoryOverlay);
  }

  function openCategories() {
    categoryContainer?.classList.add('displayed');
    categoryOverlay?.classList.remove('hidden');
  }

  function closeCategories() {
    categoryContainer?.classList.remove('displayed');
    categoryOverlay?.classList.add('hidden');
  }

  menuBtn?.addEventListener('click', openCategories);
  closeBtn?.addEventListener('click', closeCategories);
  categoryOverlay?.addEventListener('click', closeCategories);
});

// ===============================
// ✅ QR SPLASH LOADER
// ===============================
(function () {
  const splash = document.getElementById('qrSplash');
  if (!splash) return;

  try {
    const shown = sessionStorage.getItem('qrSplashShown');
    if (shown === '1') {
      splash.classList.add('is-hidden');
      return;
    }
    sessionStorage.setItem('qrSplashShown', '1');
  } catch (e) {}

  const MIN_MS = 900;

  window.addEventListener('load', () => {
    setTimeout(() => {
      splash.classList.add('is-hidden');
      setTimeout(() => splash.remove(), 450);
    }, MIN_MS);
  });
})();

// ===============================
// ✅ TABLE NUMBER
// ===============================
function syncTableNumber() {
  const serverTableEl = document.getElementById('serverTableNumber');
  const tableFromServer = serverTableEl?.dataset?.table;

  if (tableFromServer) {
    localStorage.setItem('table_number', tableFromServer);
  }

  const savedTable = localStorage.getItem('table_number');
  const tableNumberSpan = document.getElementById('tableNumber');
  if (tableNumberSpan) {
    tableNumberSpan.textContent = savedTable ? savedTable : '';
  }
}

// ===============================
// 🛒 CART SYSTEM
// ===============================
document.addEventListener('DOMContentLoaded', () => {
  syncTableNumber();

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
    if (s.startsWith('//')) return s;
    if (s.startsWith('/')) return s;
    if (s.startsWith('images/')) return `/${s}`;
    if (s.startsWith('storage/')) return `/${s}`;
    return `/images/${s}`;
  }

  function isProductAvailable(product) {
    return Number(product.is_available ?? 1) === 1;
  }

function getStockBadgeHtml(product) {
  const servings = Number(product.available_servings ?? 0);

  if (!isProductAvailable(product)) {
    return '<div class="stock-badge">Out of stock</div>';
  }

  if (servings <= 5 && servings > 0) {
    return `<div class="stock-badge stock-badge-low">Only ${servings} left</div>`;
  }

  return ''; // do not show anything if more than 5
}
  function displayProducts(categoryKey, containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const key = normalizeCategory(categoryKey);
    const filtered = menuItems.filter(
      item => normalizeCategory(item.category) === key
    );

    container.innerHTML = filtered.map(product => {
      const available = isProductAvailable(product);

      return `
        <div class="menu-item ${available ? '' : 'is-unavailable'}">
          <div class="menu-item-image">
            <img src="${resolveImagePath(product.image)}" alt="${product.name || ''}">
          </div>

          <div class="menu-item-details">
            <h3 class="menu-item-name">${product.name || ''}</h3>
            <p class="menu-item-description">${product.description || ''}</p>

            <div class="menu-item-button">
              <span class="menu-item-price">₱ ${Number(product.price || 0).toFixed(2)}</span>

              <button
                class="add-to-cart-btn"
                data-id="${product.menu_id}"
                ${available ? '' : 'disabled'}
              >
                ${available ? '+ Add' : 'Unavailable'}
              </button>
            </div>

            ${getStockBadgeHtml(product)}
          </div>
        </div>
      `;
    }).join('');

    container.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const productId = this.dataset.id;
        const productToAdd = menuItems.find(
          p => String(p.menu_id) === String(productId)
        );

        if (!productToAdd) return;

        if (!isProductAvailable(productToAdd)) {
          alert('Sorry, this item is currently out of stock.');
          return;
        }

        addToCart(productToAdd);

        if (this.classList.contains('added')) return;

        const originalText = this.textContent;
        this.textContent = 'Added';
        this.classList.add('added');
        this.disabled = true;

        setTimeout(() => {
          this.textContent = originalText;
          this.classList.remove('added');
          this.disabled = false;
        }, 2500);
      });
    });
  }

  function renderAllCategories() {
    const categories = {
      'all-day-breakfast': '.all-day-breakfast-container',
      'main-courses': '.main-courses-container',
      'pasta': '.pasta-container',
      'unlimited-premium': '.unlimited-premium-container',
      'chicken-wings': '.chicken-wings-container',
      'chicken-chops': '.chicken-chops-container',
      'overload-premium': '.overload-container',
      'solo-mini': '.solo-mini-container',
      'frappuccino': '.frappuccino-container',
      'coffee-based': '.coffee-based-container',
      'milk-based': '.milk-based-container',
      'snacks': '.snacks-container'
    };

    for (const [cat, selector] of Object.entries(categories)) {
      displayProducts(cat, selector);
    }
  }

  fetch('/api/menu', { cache: 'no-store' })
    .then(res => (res.ok ? res.json() : Promise.reject('Failed to fetch menu')))
    .then(data => {
      menuItems = Array.isArray(data) ? data : [];
      renderAllCategories();
    })
    .catch(err => console.error('Menu API error:', err));

  const cartBtn = document.querySelector('.cart-btn');
  const cartContainer = document.querySelector('.cart-container');
  const cartOverlay = document.querySelector('.cart-overlay');
  const cartClose = document.querySelector('.cart-close');
  const cartItemsContainer = document.querySelector('.cart-items');
  const cartTotalEl = document.querySelector('.cart-total');
  const proceedBtns = document.querySelectorAll('.checkout-btn');

  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  function updateCartBadge() {
    const badge = document.querySelector('.cart-badge');
    if (!badge) return;

    const count = cart.reduce((sum, item) => sum + Number(item.qty || 0), 0);

    if (count > 0) {
      badge.textContent = String(count);
      badge.classList.remove('hidden');
    } else {
      badge.textContent = '0';
      badge.classList.add('hidden');
    }
  }

  function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge();
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

  function fallbackImgTag() {
    return '/images/sample-item.png';
  }

  function renderCart() {
    if (!cartItemsContainer) return;

    cartItemsContainer.innerHTML = '';

    if (cart.length === 0) {
      cartItemsContainer.innerHTML = '<p>Your cart is empty</p>';
      if (cartTotalEl) cartTotalEl.textContent = '₱0.00';
      saveCart();
      return;
    }

    let total = 0;

    cart.forEach((item, i) => {
      const price = Number(item.price || 0);
      const qty = Number(item.qty || 0);
      total += price * qty;

      const imgSrc = resolveImagePath(item.image) || fallbackImgTag();

      cartItemsContainer.innerHTML += `
        <div class="cart-item">
          <div class="cart-item-image">
            <img src="${imgSrc}" alt="${item.name || ''}" onerror="this.src='${fallbackImgTag()}'">
          </div>

          <div class="cart-item-details">
            <h4>${item.name || ''}</h4>
            <p>₱${price.toFixed(2)}</p>

            <div class="quantity-controls">
              <button class="qty-btn minus" data-index="${i}">−</button>
              <span>${qty}</span>
              <button class="qty-btn plus" data-index="${i}">+</button>
            </div>
          </div>

          <button class="remove-item" data-index="${i}">🗑</button>
        </div>
      `;
    });

    if (cartTotalEl) cartTotalEl.textContent = `₱${total.toFixed(2)}`;
    saveCart();
  }

  cartItemsContainer?.addEventListener('click', e => {
    const btn = e.target.closest('.qty-btn, .remove-item');
    if (!btn) return;

    const i = Number(btn.dataset.index);

    if (btn.classList.contains('plus')) cart[i].qty++;
    if (btn.classList.contains('minus')) {
      cart[i].qty--;
      if (cart[i].qty <= 0) cart.splice(i, 1);
    }
    if (btn.classList.contains('remove-item')) cart.splice(i, 1);

    renderCart();
    updateCartBadge();
  });

  window.addToCart = item => {
    const existing = cart.find(c => String(c.id) === String(item.menu_id));

    if (existing) {
      existing.qty++;
    } else {
      cart.push({
        id: item.menu_id,
        name: item.name,
        price: parseFloat(item.price),
        image: resolveImagePath(item.image),
        qty: 1
      });
    }

    renderCart();
    updateCartBadge();
  };

  renderCart();
  updateCartBadge();

  const summaryOverlay = document.querySelector('.order-summary-overlay');
  const closeSummaryBtn = document.querySelector('.close-summary-btn');
  const confirmPaymentBtn = document.querySelector('.confirm-payment-btn');

  proceedBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (cart.length === 0) return alert('Your cart is empty');
      window.location.href = '/order-summary';
    });
  });

  function closeSummary() {
    const overlay = document.querySelector('.order-summary-overlay');
    const box = document.querySelector('.order-summary');
    overlay?.classList.add('hidden');
    box?.classList.add('hidden');
  }

  closeSummaryBtn?.addEventListener('click', closeSummary);
  summaryOverlay?.addEventListener('click', closeSummary);

  confirmPaymentBtn?.addEventListener('click', () => {
    cart = [];
    localStorage.removeItem('cart');
    renderCart();
    updateCartBadge();
    closeSummary();
  });
});