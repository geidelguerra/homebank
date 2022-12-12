<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'showLogin'])->name('login.show');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::get('/', function () {
    return inertia('Home');
})->name('home');

Route::resource('accounts', AccountController::class);

Route::resource('transactions', TransactionController::class);
