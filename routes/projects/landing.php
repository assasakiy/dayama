<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Rute untuk domain utama (Landing Page Utama)
Route::get('/', function () {
    return view('projects.landing.index', ['context' => 'landing']);
})->name('landing.home');
