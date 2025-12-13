// ===============================
// ✅ MENU TOGGLE (CATEGORY MENU)
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.querySelector('.menu-btn');
    const categoryContainer = document.querySelector('.category-container');
    const closeBtn = document.querySelector('.category-close');

    if (menuBtn) menuBtn.addEventListener('click', () => {
        categoryContainer.classList.toggle('displayed');
    });

    if (closeBtn) closeBtn.addEventListener('click', () => {
        categoryContainer.classList.remove('displayed');
    });
});

// ===============================
// ✅ LOAD MENU (products.js OR API)
// ===============================
let menuItems = [];

document.addEventListener('DOMContentLoaded', () => {
    // If using API
    // fetch("http://127.0.0.1:8000/api/menu")
    //     .then(res => res.json())
    //     .then(data => {
    //         menuItems = data;
    //         renderAllCategories();
    //     })
    //     .catch(err => console.error("Failed to load menu:", err));

    // If using local products.js
    menuItems = products; // products imported from products.js
    renderAllCategories();
});

// ===============================
// ✅ DISPLAY PRODUCTS BY CATEGORY
// ===============================
function displayProducts(category, containerSelector) {
    const productContainer = document.querySelector(containerSelector);
    if (!productContainer) return;

    let html = '';
    const filtered = menuItems.filter(item => item.category === category);

    filtered.forEach(product => {
        let desc = '';
        if (Array.isArray(product.description)) {
            desc = product.description.join('');
        } else {
            desc = product.description || '';
        }

        html += `
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/images/products/${product.image}" alt="${product.name}">
                </div>
                <div class="menu-item-details">
                    <h3 class="menu-item-name">${product.name}</h3>
                    <p class="menu-item-description">${desc}</p>
                    <div class="menu-item-button">
                        <span class="menu-item-price">₱ ${product.price.toFixed(2)}</span>
                        <button class="add-to-cart-btn" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">+ Add</button>
                    </div>
                </div>
            </div>
        `;
    });

    productContainer.innerHTML = html;

    // ✅ Attach Add to Cart events
    productContainer.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.id;
            const productToAdd = menuItems.find(p => p.id == productId);
            if (productToAdd) addToCart(productToAdd);
        });
    });
}

// ===============================
// ✅ RENDER ALL CATEGORIES
// ===============================
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

    for (const [category, containerSelector] of Object.entries(categories)) {
        displayProducts(category, containerSelector);
    }
}

// DISPLAY CARTTT
document.addEventListener('DOMContentLoaded', async () => {

    const cartBtn = document.querySelector('.cart-btn');
    const cartContainer = document.querySelector('.cart-container');
    const cartOverlay = document.querySelector('.cart-overlay');
    const cartClose = document.querySelector('.cart-close');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotalEl = document.querySelector('.cart-total');

    let cart = [];

    // =========================
    // FETCH MENU ITEMS FROM API
    // =========================
    try {
        const response = await fetch('/api/menu');
        const menuItems = await response.json();

        // Optional: pre-fill cart with first 2 items for UI preview
        cart = menuItems.slice(0, 2).map(item => ({
            id: item.id,
            name: item.name,
            price: parseFloat(item.price),
            image: item.image,  // only pick image, name, price
            qty: 1
        }));

        renderCart();
        openCart(); // show cart for UI preview
    } catch (error) {
        console.error('Failed to fetch menu items:', error);
    }

    // =====================
    // OPEN / CLOSE CART
    // =====================
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
            return;
        }

        let total = 0;

        cart.forEach((item, index) => {
            total += item.price * item.qty;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';

            // ONLY show name, image, price
            cartItem.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>

                <div class="cart-item-details">
                    <h4 class="item-name">${item.name}</h4>
                    <p class="item-price">₱${item.price.toFixed(2)}</p>

                    <div class="quantity-controls">
                        <button class="qty-btn minus" data-index="${index}">−</button>
                        <span class="item-qty">${item.qty}</span>
                        <button class="qty-btn plus" data-index="${index}">+</button>
                    </div>
                </div>

                <button class="remove-item" data-index="${index}">🗑</button>
            `;

            cartItemsContainer.appendChild(cartItem);
        });

        cartTotalEl.textContent = `₱${total.toFixed(2)}`;
    };

    // =====================
    // CART ACTIONS
    // =====================
    cartItemsContainer.addEventListener('click', (e) => {
        const index = e.target.dataset.index;

        if (e.target.classList.contains('plus')) cart[index].qty++;
        if (e.target.classList.contains('minus')) {
            cart[index].qty--;
            if (cart[index].qty <= 0) cart.splice(index, 1);
        }
        if (e.target.classList.contains('remove-item')) cart.splice(index, 1);

        renderCart();
    });

    // =====================
    // ADD TO CART (GLOBAL)
    // =====================
    window.addToCart = (item) => {
        const existingItem = cart.find(cartItem => cartItem.id === item.id);
        if (existingItem) existingItem.qty++;
        else cart.push({ 
            id: item.id, 
            name: item.name, 
            price: parseFloat(item.price), 
            image: item.image, 
            qty: 1 
        });

        renderCart();
        openCart();
    };
});
