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

// ===============================
// ✅ ADD TO CART FUNCTION (EXAMPLE)
// ===============================
let cart = [];

function addToCart(product) {
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({...product, qty: 1});
    }
    console.log('Cart:', cart);
    alert(`${product.name} added to cart!`);
}
