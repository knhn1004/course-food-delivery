<?php

use App\Http\Controllers\Customer\CartController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'customer',
    'as'         => 'customer.',
    'middleware' => ['auth'],
], function () {
    Route::post('cart/{product}/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
});
