<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body data-category="all-day-breakfast">

  <span id="serverTableNumber"
        data-table="{{ session('table_number') ?? '' }}"
        style="display:none;">
  </span>

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

      <button type="button" class="cart-btn" aria-label="View cart">Cart</button>

      <div class="cart-overlay hidden"></div>

      <div class="cart-container" aria-hidden="true">
          <div class="cart-header">
              <h2>Your Cart</h2>
              <button type="button" class="cart-close" aria-label="Close cart">✕</button>
          </div>

          <div class="cart-items"></div>

          <div class="cart-footer">
              <div class="cart-summary">
                  <span>Total</span>
                  <span class="cart-total">₱0.00</span>
              </div>
              <button type="button" class="checkout-btn proceed-btn">Checkout</button>
          </div>
      </div>
  </div>

  <div class="order-summary-overlay hidden"></div>

  <div class="order-summary hidden">
      <h2>Order Summary</h2>
      <div class="summary-items"></div>

      <div class="summary-total">
          <strong>Total:</strong>
          <span class="summary-total-amount">₱0.00</span>
      </div>

      <button class="confirm-payment-btn">Confirm Payment</button>
      <button class="close-summary-btn">Cancel</button>
  </div>

  <!-- Menu Bar -->
  <div class="menu-bar">
      <button type="button" class="menu-btn" aria-label="Open menu" aria-expanded="false">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect x="3" y="6" width="25" height="3" rx="1" fill="#F7B413" />
              <rect x="3" y="13" width="25" height="3" rx="1" fill="#F7B413" />
              <rect x="3" y="20" width="25" height="3" rx="1" fill="#F7B413" />
          </svg>
      </button>

      <div class="category-container">
          <button type="button" class="category-close" aria-label="Close categories">
              <svg width="25" height="25" viewBox="0 0 24 24" fill="none">
                  <path d="M18 6L6 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 6L18 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
          </button>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.breakfast') }}">
                  <img src="{{ asset('images/All-Day Breakfast.png') }}" alt="All Day Breakfast">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.main_courses') }}">
                  <img src="{{ asset('images/Main Courses.png') }}" alt="Main Courses">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.pasta') }}">
                  <img src="{{ asset('images/Pasta Menu.png') }}" alt="Pasta Menu">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.chicken') }}">
                  <img src="{{ asset('images/Chicken Menu.png') }}" alt="Chicken Menu">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.drinks') }}">
                  <img src="{{ asset('images/Drinks Menu.png') }}" alt="Drinks Menu">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.pizza') }}">
                  <img src="{{ asset('images/Pizza Menu.png') }}" alt="Pizza Menu">
              </a>
          </div></div>

          <div class="category-card"><div class="category-content">
              <a href="{{ route('menu.snacks') }}">
                  <img src="{{ asset('images/Snacks Menu.png') }}" alt="Snacks Menu">
              </a>
          </div></div>
      </div>
  </div>

  <!-- All Day Breakfast Section -->
  <div class="all-day-breakfast-container">
    @forelse($items as $item)
      @php
        $disabled = (!$item->can_make || $item->low_stock);
        $label = !$item->can_make ? 'Out of stock' : ($item->low_stock ? 'Low stock' : 'Add to cart');
      @endphp

      <div class="menu-item">
          <div class="menu-item-name">{{ $item->name }}</div>
          <div class="menu-item-description">{{ $item->description }}</div>
          <div class="menu-item-price">₱{{ number_format($item->price, 2) }}</div>

          <button
              class="add-to-cart-btn"
              data-id="{{ $item->menu_id }}"
              data-name="{{ $item->name }}"
              data-price="{{ $item->price }}"
              data-image="{{ $item->image ?? '' }}"
              {{ $disabled ? 'disabled' : '' }}
              style="
                pointer-events: {{ $disabled ? 'none' : 'auto' }};
                margin-top:8px;
                padding:8px 14px;
                border:none;
                border-radius:8px;
                font-weight:700;
                background: {{ $disabled ? '#ccc' : '#F7B413' }};
                color:#000;
                cursor: {{ $disabled ? 'not-allowed' : 'pointer' }};
                opacity: {{ $disabled ? '.6' : '1' }};
              "
          >
              {{ $label }}
          </button>
      </div>

    @empty
      <p>No items found.</p>
    @endforelse
  </div>

</body>
</html>