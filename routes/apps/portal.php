<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Application Routes (portal.dayama.test)
|--------------------------------------------------------------------------
|
| Area personal pengguna: Santri, Wali, Alumni, Guru, Pegawai.
| User-centric, cross-institution unified overview.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return response()->json([
            'app' => 'DAYAMA Portal',
            'status' => 'operational',
            'user' => auth()->user()?->name ?? 'Guest',
        ]);
    })->name('portal.home');
});
