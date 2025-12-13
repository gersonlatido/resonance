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
    fetch('http://127.0.0.1:8000/api/menu')
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
// ✅ SIMPLE CART LOGIC
// ===============================
let cart = [];

function addToCart(product) {
    const existing = cart.find(item => item.menu_id === product.menu_id);

    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({
            menu_id: product.menu_id,
            name: product.name,
            price: Number(product.price),
            qty: 1
        });
    }

    console.log('Cart:', cart);
    alert(`${product.name} added to cart!`);
}
   