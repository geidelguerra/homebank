<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportAccountsFromFileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->controller(LoginController::class)->group(function () {
    Route::get('login', 'showLogin')->name('login.show');
    Route::post('login', 'login')->name('login');
});

Route::middleware('auth:web')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    Route::get('/', HomeController::class)->name('home');

    Route::post('import/file', ImportAccountsFromFileController::class)->name('accounts.importFromFile');

    Route::resource('accounts', AccountController::class);

    Route::resource('transactions', TransactionController::class);

    Route::resource('currencies', CurrencyController::class);

    Route::resource('transfers', TransferController::class);
});
