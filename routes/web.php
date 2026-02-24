<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\PaymentReceiptController;

/*
|--------------------------------------------------------------------------
| Fix for "Route [login] not defined"
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

<<<<<<< HEAD
/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
=======



// ✅ Table Management page

Route::get('/admin/table-management', [AdminController::class, 'tableManagement'])
    ->name('admin.table-management');

Route::post('/admin/tables/{number}/toggle', [AdminController::class, 'toggleTable'])
    ->name('admin.tables.toggle');


// Customer enters via QR
// Route::get('/t/{table}', [TableController::class, 'enter'])->name('table.enter');
Route::get('/t/{table}', function ($table) {
    if ($table < 1 || $table > 10) abort(404); // adjust max tables
    session(['table_number' => (int) $table]);
    return redirect('/');
})->whereNumber('table');

// Route::get('/track/{order_code}', function ($order_code) {
//     return view('track-order', compact('order_code'));
// });



// General menu routes (customer side)
Route::get('/', function () {
    return view('home');
});
// Route::get('/payment-receit', function () {
//     return view('payment-receit');
// });


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
// Route::get('/payment-success', fn () => view('payment-success'));

// after Xendit redirect
Route::get('/payment-success', [PaymentSuccessController::class, 'show'])
    ->name('payment.success');

// receipt page
Route::get('/payment-receit', [PaymentReceiptController::class, 'show'])
    ->name('payment.receit');
Route::get('/payment-cancelled', fn () => view('payment-cancelled'));

Route::get('/feedback', function () {
    $table = session('table_number');

    if (!$table) {
        return redirect('/')->with('error', 'No table found for feedback.');
    }

    return view('feedback', ['table' => (int) $table]);
})->name('feedback.form');




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
>>>>>>> 8627347dd0f3dcaea7d48f9e4353baf0c08acbde
Route::get('admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| CASHIER ROUTES (Cashier only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'cashier'])->group(function () {

    Route::get('admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('admin/table-management', [AdminController::class, 'tableManagement'])
        ->name('admin.table-management');

<<<<<<< HEAD
    Route::get('/admin/api/orders', [AdminOrderApiController::class, 'index']);
    Route::put('/admin/api/orders/{order_code}', [OrderController::class, 'updateStatus']);

    Route::post('/orders/mark-paid', [OrderController::class, 'markPaid']);
=======
// TRACK ORDER ROUTE 
Route::get('/track-order', function () {

    $orderCode = session('order_code');

    if (!$orderCode) {
        return view('track-order', [
            'order' => null,
            'status' => 'Order not found'
        ]);
    }

    $order = \App\Models\Order::where('order_code', $orderCode)->first();

    if (!$order) {
        return view('track-order', [
            'order' => null,
            'status' => 'Order not found'
        ]);
    }

    return view('track-order', [
        'order' => $order,
        'status' => $order->status,
        'eta' => $order->eta_minutes,
    ]);

})->name('track.order');

// =======
Route::middleware(['auth', 'admin'])->get('admin/table-management', [AdminController::class, 'tableManagement'])->name('admin.table-management');
Route::get('/admin/menu-management', fn() => view('admin.menu-management'))->name('admin.menu-management');




Route::prefix('admin')->name('admin.')->group(function () {

    // Feedback page
    Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])
        ->name('feedbacks');

    // Mark reviewed button
    Route::patch('/feedbacks/{id}/review', [AdminFeedbackController::class, 'markReviewed'])
        ->name('feedback.review');

>>>>>>> 8627347dd0f3dcaea7d48f9e4353baf0c08acbde
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/inventory', [InventoryController::class, 'index'])
        ->name('admin.inventory');

<<<<<<< HEAD
    Route::get('/admin/menu-management', fn() => view('admin.menu-management'))
        ->name('admin.menu-management');

    Route::prefix('admin')->group(function () {
        Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])
            ->name('admin.feedbacks');

        Route::get('/api/feedbacks', [AdminFeedbackController::class, 'list']);
        Route::put('/api/feedbacks/{id}/reviewed', [AdminFeedbackController::class, 'markReviewed']);
    });
});
=======

// Route::get('/feedback', function () {
//     $table = session('table_number');

//     if (!$table) {
//         abort(403, 'No table assigned.');
//     }

//     return view('feedback', ['table' => (int) $table]);
// })->name('feedback.form');



Route::post('/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.store');






Route::post('/orders/mark-paid', [OrderController::class, 'markPaid']);

// Route::get('/track-order', function () {
//     return view('track-order');
// });
// Route::get('/api/track/{order_code}', [OrderController::class, 'track']);
>>>>>>> 8627347dd0f3dcaea7d48f9e4353baf0c08acbde
