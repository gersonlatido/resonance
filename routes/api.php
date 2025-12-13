<?php

use Illuminate\Support\Facades\Route;
use App\Models\MenuItem;

Route::get('/menu', function () {
    return response()->json(MenuItem::all());
});

use App\Http\Controllers\CartController;
Route::any('/cart/add', function () {
    return response()->json([
        'message' => 'Route hit!',
        'method' => request()->method(),
    ]);
});

Route::get('/cart', [CartController::class, 'index']);            // Get all cart items
Route::post('/cart/add', [CartController::class, 'add']);        // Add item to cart
Route::put('/cart/update/{id}', [CartController::class, 'update']); // Update quantity
Route::delete('/cart/remove/{id}', [CartController::class, 'remove']); // Remove item






