<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Models\MenuItem;


Route::get('/', function () {
    return view('home');
});
 
Route::get('/all-day-breakfast-menu', [MenuController::class, 'breakfast'])
    ->name('menu.breakfast');

Route::get('/main-courses-menu', [MenuController::class, 'mainCourses'])
    ->name('menu.main_courses');

Route::get('/pasta-menu', [MenuController::class, 'pasta'])
    ->name('menu.pasta');

Route::get('/chicken-menu', [MenuController::class, 'chicken'])
    ->name('menu.chicken'); 

Route::get('/drinks-menu', [MenuController::class, 'drinks'])
    ->name('menu.drinks'); 

Route::get('/pizza-menu', [MenuController::class, 'pizza'])
->name('menu.pizza'); 

Route::get('/snacks-menu', [MenuController::class, 'snacks'])
->name('menu.snacks');

Route::get('/menu', function () {
    return response()->json(MenuItem::all());
});

// Order summary page (separate page instead of modal)
Route::view('/order-summary', 'order-summary')->name('order.summary');
