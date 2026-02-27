// ===============================
// ✅ MENU TOGGLE (CATEGORY MENU)
// ===============================
document.addEventListener('DOMContentLoaded', function () {
  const menuBtn = document.querySelector('.menu-btn');
  const categoryContainer = document.querySelector('.category-container');
  const closeBtn = document.querySelector('.category-close');

  // ✅ create overlay for categories
  let categoryOverlay = document.querySelector('.category-overlay');
  if (!categoryOverlay) {
    categoryOverlay = document.createElement('div');
    categoryOverlay.className = 'category-overlay hidden';
    document.body.appendChild(categoryOverlay);
  }

  function openCategories() {
    categoryContainer.classList.add('displayed');
    categoryOverlay.classList.remove('hidden');
  }

  function closeCategories() {
    categoryContainer.classList.remove('displayed');
    categoryOverlay.classList.add('hidden');
  }

  menuBtn?.addEventListener('click', openCategories);
  closeBtn?.addEventListener('click', closeCategories);
  categoryOverlay?.addEventListener('click', closeCategories);
});

// ===============================
// ✅ TABLE NUMBER (QR -> session -> localStorage)
// ===============================
function syncTableNumber() {
  const serverTableEl = document.getElementById('serverTableNumber');
  const tableFromServer = serverTableEl?.dataset?.table;

  // If session has table_number, store to localStorage
  if (tableFromServer) {
    localStorage.setItem('table_number', tableFromServer);
  }

  // Always display from localStorage if available
  const savedTable = localStorage.getItem('table_number');
  const tableNumberSpan = document.getElementById('tableNumber');
  if (tableNumberSpan) {
    tableNumberSpan.textContent = savedTable ? savedTable : '';
  }
}

// ===============================
// 🛒 CART SYSTEM (LOCALSTORAGE)
// ===============================
document.addEventListener('DOMContentLoaded', () => {
  syncTableNumber();

  // ===============================
  // ✅ MENU DATA (FROM API) - FIXED
  // ===============================
  let menuItems = [];

  // normalize so "main-courses", "Main Courses", "Main-Courses" match
  function normalizeCategory(str) {
    return String(str || '')
      .toLowerCase()
      .replace(/&/g, 'and')
      .replace(/[^a-z0-9]+/g, '-')   // spaces -> -
      .replace(/^-+|-+$/g, '');      // trim -
  }

  // ✅ IMPORTANT: Make image path safe everywhere (menu + cart)
  function resolveImagePath(img) {
    const s = String(img || '').trim();
    if (!s) return '';                    // no image
    if (s.startsWith('http')) return s;   // full URL
    if (s.startsWith('//')) return s;     // protocol-relative URL
    if (s.startsWith('/')) return s;      // already absolute like /images/x.png or /storage/x.png

    // If DB stores "images/xxx.png"
    if (s.startsWith('images/')) return `/${s}`;
    // If DB stores "storage/xxx.png"
    if (s.startsWith('storage/')) return `/${s}`;

    // Default: you store in public/images/
    return `/images/${s}`;

    // If you actually store in storage/app/public/, use this instead:
    // return `/storage/${s}`;
  }

  function displayProducts(categoryKey, containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    const key = normalizeCategory(categoryKey);

    // ✅ match by normalized category
    const filtered = menuItems.filter(item => normalizeCategory(item.category) === key);

    container.innerHTML = filtered.map(product => {
      const available = Number(product.is_available ?? 1) === 1;

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

            ${available ? '' : '<div class="stock-badge">Out of stock</div>'}
          </div>
        </div>
      `;
    }).join('');

    // ✅ attach click once (no double listeners)
    container.querySelectorAll('.add-to-cart-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const productId = this.dataset.id;
        const productToAdd = menuItems.find(p => String(p.menu_id) === String(productId));
        if (!productToAdd) return;

        // ✅ block if unavailable
        if (Number(productToAdd.is_available ?? 1) !== 1) {
          alert('Sorry, this item is currently out of stock.');
          return;
        }

        addToCart(productToAdd);

        // "Added" state
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

  // ✅ Fetch from API (no cache so DB updates show instantly)
  fetch('/api/menu', { cache: 'no-store' })
    .then(res => res.ok ? res.json() : Promise.reject('Failed to fetch menu'))
    .then(data => {
      menuItems = Array.isArray(data) ? data : [];
      renderAllCategories();
    })
    .catch(err => console.error('Menu API error:', err));

  // ===============================
  // CART SYSTEM
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
    cartContainer.classList.add('active');
    cartOverlay.classList.remove('hidden');
  }

  function closeCart() {
    cartContainer.classList.remove('active');
    cartOverlay.classList.add('hidden');
  }

  cartBtn?.addEventListener('click', openCart);
  cartClose?.addEventListener('click', closeCart);
  cartOverlay?.addEventListener('click', closeCart);

  // ✅ fallback image if missing
  function fallbackImgTag(name) {
    // optional: point to your placeholder in public/images/
    return `/images/sample-item.png`;
  }

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
      const price = Number(item.price || 0);
      const qty = Number(item.qty || 0);
      total += price * qty;

      const imgSrc = resolveImagePath(item.image) || fallbackImgTag(item.name);

      cartItemsContainer.innerHTML += `
        <div class="cart-item">
          <div class="cart-item-image">
            <img src="${imgSrc}" alt="${item.name || ''}" onerror="this.src='${fallbackImgTag(item.name)}'">
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

    cartTotalEl.textContent = `₱${total.toFixed(2)}`;
    saveCart();
  }

  cartItemsContainer.addEventListener('click', e => {
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
  });

  // ✅ IMPORTANT FIX: store resolved image path in cart
  window.addToCart = (item) => {
    const existing = cart.find(c => String(c.id) === String(item.menu_id));
    if (existing) {
      existing.qty++;
    } else {
      cart.push({
        id: item.menu_id,
        name: item.name,
        price: parseFloat(item.price),
        image: resolveImagePath(item.image), // ✅ FIXED HERE
        qty: 1
      });
    }
    renderCart();
  };

  renderCart(); // render saved cart on load

  // ===============================
  // PROCEED TO PAYMENT
  // ===============================
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
    alert('Order placed successfully!');
    cart = [];
    localStorage.removeItem('cart');
    renderCart();
    closeSummary();
  });
});