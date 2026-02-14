<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Models\MenuItem;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderApiController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;



// Customer enters via QR
// Route::get('/t/{table}', [TableController::class, 'enter'])->name('table.enter');
Route::get('/t/{table}', function ($table) {
    if ($table < 1 || $table > 10) abort(404); // adjust max tables
    session(['table_number' => (int) $table]);
    return redirect('/');
})->whereNumber('table');

Route::get('/track/{order_code}', function ($order_code) {
    return view('track-order', compact('order_code'));
});



// General menu routes (customer side)
Route::get('/', function () {
    return view('home');
});
Route::get('/payment-receit', function () {
    return view('payment-receit');
});
Route::get('/all-day-breakfast-menu', [MenuController::class, 'breakfast'])->name('menu.breakfast');
Route::get('/main-courses-menu', [MenuController::class, 'mainCourses'])->name('menu.main_courses');
Route::get('/pasta-menu', [MenuController::class, 'pasta'])->name('menu.pasta');
Route::get('/chicken-menu', [MenuController::class, 'chicken'])->name('menu.chicken'); 
Route::get('/drinks-menu', [MenuController::class, 'drinks'])->name('menu.drinks'); 
Route::get('/pizza-menu', [MenuController::class, 'pizza'])->name('menu.pizza');
Route::get('/snacks-menu', [MenuController::class, 'snacks'])->name('menu.snacks');

Route::get('/menu', function () {
    return response()->json(MenuItem::all());
});

// Order summary page
Route::view('/order-summary', 'order-summary')->name('order.summary');

// Payment routes
use App\Http\Controllers\PaymentController;

Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
Route::get('/payment-success', fn () => view('payment-success'));
Route::get('/payment-cancelled', fn () => view('payment-cancelled'));

Route::get('/feedback', [PaymentController::class, 'showFeedback'])->name('feedback.show');



Route::get('/admin/api/orders', [AdminOrderApiController::class, 'index']);
// Route::put('/admin/orders/{order_code}', [OrderController::class, 'updateStatus']);
Route::put('/admin/api/orders/{order_code}', [OrderController::class, 'updateStatus']);









// // Payment routes
// use App\Http\Controllers\PaymentController;

// Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
// Route::post('/payment/initiate', [PaymentController::class, 'initiate']);


// Route::view('/payment-success', 'payment-success');
// Route::view('/payment-failed', 'payment-failed');
// =======

// Admin routes (login, dashboard, and logout)
Route::get('admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'adminLogin']);
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');


// Admin dashboard route (protected by 'auth' and 'admin' middleware)
Route::middleware(['auth', 'admin'])->get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

// Route for handling the orders data from localStorage (POST request)
Route::post('admin/storeOrdersData', [AdminController::class, 'storeOrdersData'])->name('admin.storeOrdersData');

// TRACK ORDER ROUTE 
Route::get('/track-order', function () {
    return view('track-order');
})->name('track.order');

// =======
Route::middleware(['auth', 'admin'])->get('admin/table-management', [AdminController::class, 'tableManagement'])->name('admin.table-management');
Route::get('/admin/menu-management', fn() => view('admin.menu-management'))->name('admin.menu-management');





Route::post('/orders/mark-paid', [OrderController::class, 'markPaid']);

// Route::get('/track-order', function () {
//     return view('track-order');
// });
Route::get('/api/track/{order_code}', [OrderController::class, 'track']);
