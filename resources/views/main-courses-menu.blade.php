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
        <button type="button" class="cart-btn" aria-label="View cart">Cart</button>
    </div>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <!-- Hamburger/menu button -->
        <button type="button" class="menu-btn" aria-label="Open menu" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="6" width="25" height="3" rx="1" fill="#F7B413" />
                <rect x="3" y="13" width="25" height="3" rx="1" fill="#F7B413" />
                <rect x="3" y="20" width="25" height="3" rx="1" fill="#F7B413" />
            </svg>
        </button>

        <!-- Category Panel -->
        <div class="category-container">
            <button type="button" class="category-close" aria-label="Close categories">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none">
                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Category Cards -->
            <div class="category-card">
                <div class="category-content">
                    <a href="{{ route('menu.breakfast') }}">
                        <img src="{{ asset('images/All-Day Breakfast.png') }}" alt="All Day Breakfast">
                    </a>
                </div>
            </div>
            

             <div class="category-card">
                <div class="category-content">
                    <a href="{{ route('menu.main_courses') }}">
                        <img src="{{ asset('images/Main Courses.png') }}" alt="Main Courses">
                    </a>
                </div>
            </div> 

               <div class="category-card">
                <div class="category-content">
                    <a href="{{ route('menu.pasta') }}">
                        <img src="{{ asset('images/Pasta Menu.png') }}" alt="Pasta Menu">
                    </a>
                </div>
            </div> 

               <div class="category-card">
                <div class="category-content">
                    <a href="{{ route('menu.chicken') }}">
                        <img src="{{ asset('images/Chicken Menu.png') }}" alt="Chicken Menu">
                    </a>
                </div>
            </div> 

            <div class="category-card">
                    <div class="category-content">
                        <a href="{{ route('menu.drinks') }}">
                            <img src="{{ asset('images/Drinks Menu.png') }}" alt="Drinks Menu">
                        </a>
                    </div>
            </div> 

                     <div class="category-card">
                         <div class="category-content">
                            <a href="{{ route('menu.pizza') }}">
                                <img src="{{ asset('images/Pizza Menu.png') }}" alt="Pizza Menu">
                            </a>
                        </div>
                    </div> 


             <div class="category-card">
                <div class="category-content">
                    <a href="{{ route('menu.snacks') }}">
                        <img src="{{ asset('images/Snacks Menu.png') }}" alt="Snacks Menu">
                    </a>
                </div>
            </div> 
         
        </div>
    </div>

    <!-- Main courses Breakfast Section -->
    <div class="main-courses-container">
    </div>
</body>
</html>
