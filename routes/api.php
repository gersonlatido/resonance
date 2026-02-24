<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecipeController;

// =========================
// MENU API (uses MenuController@index which includes can_make)
// =========================
Route::get('/menu', [MenuController::class, 'index']);
Route::post('/menu', [MenuController::class, 'store']);
Route::put('/menu/{menu_id}', [MenuController::class, 'update']);
Route::delete('/menu/{menu_id}', [MenuController::class, 'destroy']);

// =========================
// ORDER TRACKING / UPDATE
// =========================
Route::get('/orders/{order_code}', [OrderController::class, 'track']);
Route::put('/orders/{order_code}', [OrderController::class, 'updateStatus']);

// =========================
// RECIPES (ingredients per menu)
// =========================
Route::get('/menu/{menu_id}/ingredients', [RecipeController::class, 'index']);
Route::post('/menu/{menu_id}/ingredients', [RecipeController::class, 'store']);
Route::delete('/menu/{menu_id}/ingredients/{ingredient_id}', [RecipeController::class, 'destroy']);