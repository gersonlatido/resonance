<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| Fix for "Route [login] not defined"
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
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

    Route::get('/admin/api/orders', [AdminOrderApiController::class, 'index']);
    Route::put('/admin/api/orders/{order_code}', [OrderController::class, 'updateStatus']);

    Route::post('/orders/mark-paid', [OrderController::class, 'markPaid']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/inventory', [InventoryController::class, 'index'])
        ->name('admin.inventory');

    Route::get('/admin/menu-management', fn() => view('admin.menu-management'))
        ->name('admin.menu-management');

    Route::prefix('admin')->group(function () {
        Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])
            ->name('admin.feedbacks');

        Route::get('/api/feedbacks', [AdminFeedbackController::class, 'list']);
        Route::put('/api/feedbacks/{id}/reviewed', [AdminFeedbackController::class, 'markReviewed']);
    });
});