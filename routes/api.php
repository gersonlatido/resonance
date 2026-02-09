<?php

use Illuminate\Support\Facades\Route;
use App\Models\MenuItem;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;



Route::get('/menu', function () {
    return response()->json(MenuItem::all());
});



// Route::any('/cart/add', function () {
//     return response()->json([
//         'message' => 'Route hit!',
//         'method' => request()->method(),
//     ]);
// });

// Route::get('/cart', [CartController::class, 'index']);            // Get all cart items
// Route::post('/cart/add', [CartController::class, 'add']);        // Add item to cart
// Route::put('/cart/update/{id}', [CartController::class, 'update']); // Update quantity
// Route::delete('/cart/remove/{id}', [CartController::class, 'remove']); // Remove item



Route::get('/menu', [MenuController::class, 'index']);
Route::post('/menu', [MenuController::class, 'store']);           // ✅ ADD
Route::put('/menu/{menu_id}', [MenuController::class, 'update']); // ✅ ADD
Route::delete('/menu/{menu_id}', [MenuController::class, 'destroy']); // ✅ ADD



