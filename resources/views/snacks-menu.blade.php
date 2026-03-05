<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- JS -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-left">
            <div class="logo-image">
                <img src="{{ asset('images/navbar-logo.png') }}" alt="Company logo" class="navbar-logo">
            </div>
            <div class="span">
                <span class="navbar-table">
                    Table #: <span id="tableNumber"></span>
                </span>
            </div>
        </div>
        <button type="button" class="cart-btn" aria-label="View cart">
  Cart
  <span class="cart-badge hidden" aria-label="Cart items count">0</span>
</button>

                       
        <!-- Cart Overlay -->
        <div class="cart-overlay hidden"></div>

        <!-- Cart Sidebar -->
        <div class="cart-container" aria-hidden="true">
            <!-- Cart Header -->
            <div class="cart-header">
                <h2>Your Cart</h2>
                <button type="button" class="cart-close" aria-label="Close cart">
                    ✕
                </button>
            </div>

            <!-- Cart Items -->
            <div class="cart-items">
                <!-- Cart Item -->
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="{{ asset('images/sample-item.png') }}" alt="Item name">
                    </div>

                    <div class="cart-item-details">
                        <h4 class="item-name">BEEFSILOG</h4>
                        <p class="item-price">₱150.00</p>

                        <div class="quantity-controls">
                            <button type="button" class="qty-btn minus" >−</button>
                            <span class="item-qty">1</span>
                            <button type="button" class="qty-btn plus">+</button>
                        </div>
                    </div>

                    <button type="button" class="remove-item" aria-label="Remove item">
                        🗑
                    </button>
                </div>
                <!-- End Cart Item -->
            </div>

            <!-- Cart Footer -->
            <div class="cart-footer">
                <div class="cart-summary">
                    <span>Total</span>
                    <span class="cart-total">₱150.00</span>
                </div>

                <button type="button" class="checkout-btn">
                    Checkout
                </button>
            </div>
        </div>

                
            </div>
    </div>

    <!-- Menu Bar -->
  <!-- CATEGORY SCROLLER  -->
<div class="fp-cats-wrap">

  <button class="fp-scroll-btn fp-left" id="fpArrowLeft" type="button" aria-label="Scroll left">‹</button>

  <div class="fp-cats" id="fpCats">

    <a href="{{ route('menu.breakfast') }}"
       class="fp-cat {{ request()->routeIs('menu.breakfast') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/beefsilog.png') }}" alt="Breakfast">
      </div>
      <div class="fp-cat-label">Breakfast</div>
    </a>

    <a href="{{ route('menu.main_courses') }}"
       class="fp-cat {{ request()->routeIs('menu.main_courses') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/STEAK RICE.png') }}" alt="Main Courses">
      </div>
      <div class="fp-cat-label">Main Courses</div>
    </a>

    <a href="{{ route('menu.pasta') }}"
       class="fp-cat {{ request()->routeIs('menu.pasta') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/creamy-pesto-pasta.png') }}" alt="Pasta">
      </div>
      <div class="fp-cat-label">Pasta</div>
    </a>

    <a href="{{ route('menu.chicken') }}"
       class="fp-cat {{ request()->routeIs('menu.chicken') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/unli-basic.png') }}" alt="Chicken">
      </div>
      <div class="fp-cat-label">Chicken</div>
    </a>

    <a href="{{ route('menu.drinks') }}"
       class="fp-cat {{ request()->routeIs('menu.drinks') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/coffee-cream-frappe.png') }}" alt="Drinks">
      </div>
      <div class="fp-cat-label">Drinks</div>
    </a>

    <a href="{{ route('menu.pizza') }}"
       class="fp-cat {{ request()->routeIs('menu.pizza') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/6-cheese.png') }}" alt="Pizza">
      </div>
      <div class="fp-cat-label">Pizza</div>
    </a>

    <a href="{{ route('menu.snacks') }}"
       class="fp-cat {{ request()->routeIs('menu.snacks') ? 'is-active' : '' }}">
      <div class="fp-cat-card">
        <img src="{{ asset('images/chicken-poppers.png') }}" alt="Snacks">
      </div>
      <div class="fp-cat-label">Snacks</div>
    </a>

  </div>

  <button class="fp-scroll-btn fp-right" id="fpArrowRight" type="button" aria-label="Scroll right">›</button>

</div>

    <!-- Snacks Section -->
    <div class="snacks-container">
    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {

  const cats     = document.getElementById('fpCats');
  const leftBtn  = document.getElementById('fpArrowLeft');
  const rightBtn = document.getElementById('fpArrowRight');

  if (!cats || !leftBtn || !rightBtn) return;

  const KEY = "fpCatsScrollPos"; // shared for all category pages
  let scrollTimer = null;

  function items() {
    return Array.from(cats.querySelectorAll('.fp-cat'));
  }

  function maxScrollLeft() {
    return cats.scrollWidth - cats.clientWidth;
  }

  function passedFirstItem() {
    const list = items();
    if (list.length < 2) return false;
    const secondLeft = list[1].offsetLeft;
    return cats.scrollLeft >= (secondLeft - 6);
  }

  function atEnd() {
    return cats.scrollLeft >= (maxScrollLeft() - 4);
  }

  function updateArrows() {
    if (maxScrollLeft() <= 1) {
      leftBtn.style.display = "none";
      rightBtn.style.display = "none";
      return;
    }

    // left appears only starting from 2nd category
    leftBtn.style.display = passedFirstItem() ? "flex" : "none";

    // right hidden only at end
    rightBtn.style.display = atEnd() ? "none" : "flex";
  }

  function hideArrowsWhileScrolling() {
    leftBtn.style.display = "none";
    rightBtn.style.display = "none";
  }

  // ✅ move 1 category per click
  function scrollOne(dir) {
    const list = items();
    if (!list.length) return;

    const x = cats.scrollLeft;

    let currentIndex = 0;
    for (let i = 0; i < list.length; i++) {
      if (list[i].offsetLeft <= x + 6) currentIndex = i;
      else break;
    }

    let targetIndex = currentIndex + dir;
    if (targetIndex < 0) targetIndex = 0;
    if (targetIndex > list.length - 1) targetIndex = list.length - 1;

    cats.scrollTo({ left: list[targetIndex].offsetLeft, behavior: 'smooth' });
  }

  leftBtn.addEventListener('click', function () { scrollOne(-1); });
  rightBtn.addEventListener('click', function () { scrollOne(1); });

  // ✅ save while scrolling + arrows behavior
  cats.addEventListener('scroll', function () {
    // save current position
    sessionStorage.setItem(KEY, String(cats.scrollLeft));

    hideArrowsWhileScrolling();
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(updateArrows, 180);
  }, { passive: true });

  // ✅ save before navigation when clicking a category link
  cats.addEventListener('click', function (e) {
    const link = e.target.closest('a.fp-cat');
    if (!link) return;
    sessionStorage.setItem(KEY, String(cats.scrollLeft));
  });

  window.addEventListener('resize', function () {
    // keep saved position on resize too
    sessionStorage.setItem(KEY, String(cats.scrollLeft));
    updateArrows();
  });

  // ✅ restore saved position; if none, center active category
  function restoreOrCenterActive() {
    const oldSnap = cats.style.scrollSnapType;
    cats.style.scrollSnapType = "none";

    const saved = sessionStorage.getItem(KEY);

    if (saved !== null) {
      const value = parseFloat(saved) || 0;
      cats.scrollTo({ left: value, top: 0, behavior: "auto" });
      cats.scrollLeft = value;
    } else {
      const active = cats.querySelector('.fp-cat.is-active');
      if (active) {
        const target =
          active.offsetLeft - (cats.clientWidth / 2) + (active.offsetWidth / 2);
        const clamped = Math.max(0, Math.min(target, maxScrollLeft()));
        cats.scrollTo({ left: clamped, top: 0, behavior: "auto" });
        cats.scrollLeft = clamped;
      } else {
        cats.scrollTo({ left: 0, top: 0, behavior: "auto" });
        cats.scrollLeft = 0;
      }
    }

    requestAnimationFrame(() => {
      cats.style.scrollSnapType = oldSnap || "x mandatory";
      updateArrows();
    });
  }

  // Run restore after layout settles (important for mobile)
  restoreOrCenterActive();
  window.addEventListener('load', restoreOrCenterActive);
  window.addEventListener('pageshow', restoreOrCenterActive);

});
</script>
</body>
</html>
