<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLogin'])->name('login.show');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:web')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    Route::get('/', HomeController::class)->name('home');

    Route::resource('accounts', AccountController::class);

    Route::resource('transactions', TransactionController::class);

    Route::resource('currencies', CurrencyController::class);
});
