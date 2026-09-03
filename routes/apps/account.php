<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Redirect root domain account.test-blog.test ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes (login/register/forgot password dll)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [\App\Http\Controllers\Dashboard\AuthController::class, 'login'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Dashboard\AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/register', [\App\Http\Controllers\Dashboard\AuthController::class, 'register'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Dashboard\AuthController::class, 'store'])->name('register.store');
});

// Route jika user ingin logout dari domain auth khusus (jika diperlukan)
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [\App\Http\Controllers\Dashboard\AuthController::class, 'logout'])->name('logout');
});
