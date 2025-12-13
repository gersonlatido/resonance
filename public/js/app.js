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
// ✅ LOAD MENU FROM LARAVEL API
// ===============================
let menuItems = [];

document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/menu')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch menu');
            }
            return response.json();
        })
        .then(data => {
            menuItems = data;
            renderAllCategories();
        })
        .catch(error => {
            console.error('Error loading menu:', error);
        });
});

// ===============================
// ✅ DISPLAY PRODUCTS BY CATEGORY
// ===============================
function displayProducts(category, containerSelector) {
    const productContainer = document.querySelector(containerSelector);
    if (!productContainer) return;

    const filtered = menuItems.filter(item => item.category === category);
    let html = '';

    filtered.forEach(product => {
        html += `
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="${product.image}" alt="${product.name}">  <!-- Fixed: No extra /images/products/ -->
                </div>
                <div class="menu-item-details">
                    <h3 class="menu-item-name">${product.name}</h3>
                    <p class="menu-item-description">${product.description}</p>
                    <div class="menu-item-button">
                        <span class="menu-item-price">₱ ${Number(product.price).toFixed(2)}</span>
                        <button 
                            class="add-to-cart-btn" 
                            data-id="${product.menu_id}">
                            + Add
                        </button>
                    </div>
                </div>
            </div>
        `;
    });

    productContainer.innerHTML = html;

    // ✅ Add-to-cart events
    productContainer.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.id;
            const productToAdd = menuItems.find(p => p.menu_id === productId);
            if (productToAdd) {
                addToCart(productToAdd);
            }
        });
    });
}
// ===============================
// ✅ RENDER ALL CATEGORIES
// ===============================
function renderAllCategories() {
    const categories = {
        "All Day Breakfast": ".all-day-breakfast-container",
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

    for (const [category, selector] of Object.entries(categories)) {
        displayProducts(category, selector);
    }
}

// ===============================
// 🛒 CART SYSTEM (LOCALSTORAGE)
// ===============================
document.addEventListener('DOMContentLoaded', () => {

    const cartBtn = document.querySelector('.cart-btn');
    const cartContainer = document.querySelector('.cart-container');
    const cartOverlay = document.querySelector('.cart-overlay');
    const cartClose = document.querySelector('.cart-close');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotalEl = document.querySelector('.cart-total');

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    const saveCart = () => {
        localStorage.setItem('cart', JSON.stringify(cart));
    };

    const openCart = () => {
        cartContainer.classList.add('active');
        cartOverlay.classList.remove('hidden');
    };

    const closeCart = () => {
        cartContainer.classList.remove('active');
        cartOverlay.classList.add('hidden');
    };

    cartBtn.addEventListener('click', openCart);
    cartClose.addEventListener('click', closeCart);
    cartOverlay.addEventListener('click', closeCart);

    // =====================
    // RENDER CART
    // =====================
    const renderCart = () => {
        cartItemsContainer.innerHTML = '';

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `<p>Your cart is empty</p>`;
            cartTotalEl.textContent = '₱0.00';
            saveCart();
            return;
        }

        let total = 0;

        cart.forEach((item, index) => {
            total += item.price * item.qty;

            cartItemsContainer.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>

                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <p>₱${item.price.toFixed(2)}</p>

                        <div class="quantity-controls">
                            <button class="qty-btn minus" data-index="${index}">−</button>
                            <span>${item.qty}</span>
                            <button class="qty-btn plus" data-index="${index}">+</button>
                        </div>
                    </div>

                    <button class="remove-item" data-index="${index}">🗑</button>
                </div>
            `;
        });

        cartTotalEl.textContent = `₱${total.toFixed(2)}`;
        saveCart();
    };

    // =====================
    // CART BUTTON ACTIONS
    // =====================
    cartItemsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('.qty-btn, .remove-item');
        if (!btn) return;

        const index = btn.dataset.index;

        if (btn.classList.contains('plus')) cart[index].qty++;
        if (btn.classList.contains('minus')) {
            cart[index].qty--;
            if (cart[index].qty <= 0) cart.splice(index, 1);
        }
        if (btn.classList.contains('remove-item')) cart.splice(index, 1);

        renderCart();
    });

    // =====================
    // ADD TO CART (GLOBAL)
    // =====================
    window.addToCart = (item) => {
        const existing = cart.find(c => c.id === item.menu_id);

        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                id: item.menu_id,
                name: item.name,
                price: parseFloat(item.price),
                image: item.image,
                qty: 1
            });
        }

        renderCart();
        openCart();
    };

    // ✅ render saved cart on load
    renderCart();
});