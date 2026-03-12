<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Models\MenuItem;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesStockReportController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| QR ENTRY
|--------------------------------------------------------------------------
*/
Route::get('/t/{table}', [TableController::class, 'enter'])
    ->whereNumber('table')
    ->name('table.enter');

Route::post('/t/select', [TableController::class, 'select'])
    ->name('table.select');

/*
|--------------------------------------------------------------------------
| CUSTOMER PAGES
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('home'));

Route::get('/all-day-breakfast-menu', [MenuController::class, 'breakfast'])->name('menu.breakfast');
Route::get('/main-courses-menu', [MenuController::class, 'mainCourses'])->name('menu.main_courses');
Route::get('/pasta-menu', [MenuController::class, 'pasta'])->name('menu.pasta');
Route::get('/chicken-menu', [MenuController::class, 'chicken'])->name('menu.chicken');
Route::get('/drinks-menu', [MenuController::class, 'drinks'])->name('menu.drinks');
Route::get('/pizza-menu', [MenuController::class, 'pizza'])->name('menu.pizza');
Route::get('/snacks-menu', [MenuController::class, 'snacks'])->name('menu.snacks');

Route::get('/menu', fn() => response()->json(MenuItem::all()));

Route::view('/order-summary', 'order-summary')->name('order.summary');
Route::post('/done-eating', [TableController::class, 'doneEating'])->name('table.done_eating');

/*
|--------------------------------------------------------------------------
| PAYMENT FLOW (XENDIT)
|--------------------------------------------------------------------------
*/
Route::get('/payment', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/initiate', [PaymentController::class, 'initiate']);

Route::get('/payment-success', [PaymentSuccessController::class, 'show'])
    ->name('payment.success');

Route::get('/payment-receipt/{order_code?}', [PaymentReceiptController::class, 'show'])
    ->name('payment.receipt');

Route::get('/payment-receit', function () {
    $orderCode = session('order_code');

    if (!$orderCode) {
        return redirect('/');
    }

    return redirect()->route('payment.receipt', ['order_code' => $orderCode]);
});

Route::get('/payment-cancelled', fn () => view('payment-cancelled'));

/*
|--------------------------------------------------------------------------
| FEEDBACK
|--------------------------------------------------------------------------
*/
Route::get('/feedback', function () {
    $table = session('table_number');

    if (!$table) {
        return redirect('/')->with('error', 'No table found for feedback.');
    }

    return view('feedback', ['table' => (int) $table]);
})->name('feedback.form');

Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

/*
|--------------------------------------------------------------------------
| TRACK ORDER (SESSION-BASED)
|--------------------------------------------------------------------------
*/
Route::get('/track-order', function () {
    $orderCode = session('order_code');

    if (!$orderCode) {
        return view('track-order', [
            'order' => null,
            'status' => 'Order not found',
        ]);
    }

    $order = \App\Models\Order::where('order_code', $orderCode)->first();

    if (!$order) {
        return view('track-order', [
            'order' => null,
            'status' => 'Order not found',
        ]);
    }

    return view('track-order', [
        'order' => $order,
        'status' => $order->status,
        'eta' => $order->eta_minutes,
    ]);
})->name('track.order');

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/
Route::get('admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'adminLogin']);
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| SHARED STAFF ROUTES (ADMIN + CASHIER)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {

    /*
    |-----------------------------------------------------------------------
    | DASHBOARD / ORDER MANAGEMENT
    |-----------------------------------------------------------------------
    */
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('admin/storeOrdersData', [AdminController::class, 'storeOrdersData'])->name('admin.storeOrdersData');
Route::get('/admin/dashboard-analytics', [AdminController::class, 'analytics'])
    ->name('admin.dashboard.analytics');
    /*
    |-----------------------------------------------------------------------
    | DAILY SALES REPORT
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/daily-sales-report', [AdminController::class, 'dailySalesReport'])
        ->name('admin.daily-sales-report');

    /*
    |-----------------------------------------------------------------------
    | SALES REPORT EXPORT
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/sales-report/export/csv', [AdminController::class, 'exportSalesReportCsv'])
        ->name('admin.sales-report.export.csv');

    Route::get('/admin/sales-report/print', [AdminController::class, 'printSalesReport'])
        ->name('admin.sales-report.print');

    Route::get('/admin/sales-report/export/xls', [AdminController::class, 'exportSalesReportXls'])
        ->name('admin.sales-report.export.xls');

    /*
    |-----------------------------------------------------------------------
    | TABLE MANAGEMENT
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/table-management', [AdminController::class, 'tableManagement'])
        ->name('admin.table-management');

    Route::post('/admin/tables/{number}/toggle', [AdminController::class, 'toggleTable'])
        ->name('admin.tables.toggle');

    /*
    |-----------------------------------------------------------------------
    | ORDER API
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/api/orders', [AdminOrderApiController::class, 'index']);
    Route::put('/admin/api/orders/{order_code}', [OrderController::class, 'updateStatus']);
});

/*
|--------------------------------------------------------------------------
| ADMIN-ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    /*
    |-----------------------------------------------------------------------
    | SALES & STOCK REPORTS
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/sales-stock-reports', [SalesStockReportController::class, 'index'])
        ->name('admin.sales-stock-reports');

    Route::get('/admin/sales-stock-reports/print', [SalesStockReportController::class, 'print'])
        ->name('admin.sales-stock-reports.print');

    Route::get('/admin/sales-stock-reports/export/csv', [SalesStockReportController::class, 'exportCsv'])
        ->name('admin.sales-stock-reports.export.csv');

    /*
    |-----------------------------------------------------------------------
    | INVENTORY
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory');

    Route::post('/admin/inventory/ingredients', [InventoryController::class, 'storeIngredient'])
        ->name('admin.inventory.ingredients.store');

    Route::put('/admin/inventory/ingredients/{ingredient}', [InventoryController::class, 'updateIngredient'])
        ->name('admin.inventory.ingredients.update');

    Route::post('/admin/inventory/{ingredient}/stock-in', [InventoryController::class, 'stockIn'])
        ->name('admin.inventory.stockin');

    Route::post('/admin/inventory/{ingredient}/stock-out', [InventoryController::class, 'stockOut'])
        ->name('admin.inventory.stockout');

    Route::get('/admin/inventory/recompute-all', [InventoryController::class, 'recomputeAll'])
        ->name('admin.inventory.recompute-all');

    /*
    |-----------------------------------------------------------------------
    | MENU MANAGEMENT
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/menu-management', fn () => view('admin.menu-management'))
        ->name('admin.menu-management');

    /*
    |-----------------------------------------------------------------------
    | USER MANAGEMENT
    |-----------------------------------------------------------------------
    */
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{employee_id}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{employee_id}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{employee_id}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    /*
    |-----------------------------------------------------------------------
    | FEEDBACK MANAGEMENT
    |-----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks');
        Route::patch('/feedbacks/{id}/review', [AdminFeedbackController::class, 'markReviewed'])->name('feedback.review');
    });
});

/*
|--------------------------------------------------------------------------
| ORDER STATUS UPDATE (PUBLIC/INTERNAL)
|--------------------------------------------------------------------------
*/
Route::post('/orders/mark-paid', [OrderController::class, 'markPaid']);

//     Route::get('/recompute-menus', function () {

//     $menus = \App\Models\MenuItem::pluck('menu_id');

//     foreach ($menus as $menuId) {

//         $recipes = \App\Models\Recipe::with('ingredient')
//             ->where('menu_id', $menuId)
//             ->get();

//         $servings = [];

//         foreach ($recipes as $recipe) {

//             $ingredient = $recipe->ingredient;

//             if (!$ingredient) continue;

//             $need  = (float)$recipe->qty_needed;
//             $stock = (float)$ingredient->stock_qty;

//             if ($need <= 0) continue;

//             $servings[] = floor($stock / $need);
//         }

//         $available = count($servings) ? min($servings) : 0;

//         \App\Models\MenuItem::where('menu_id',$menuId)->update([
//             'available_servings' => $available,
//             'is_available' => $available > 0 ? 1 : 0
//         ]);
//     }

//     return "Menus recomputed!";
// });