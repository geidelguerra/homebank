<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLogin'])->name('login.show');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:web')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    Route::resource('accounts', AccountController::class);
});

Route::get('/', function () {
    return inertia('Home');
})->name('home');


Route::resource('transactions', TransactionController::class);
