<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PSB Application Routes (psb.dayama.test)
|--------------------------------------------------------------------------
|
| Penerimaan Santri/Siswa Baru terpusat seluruh lembaga.
|
*/

Route::get('/', function () {
    return response()->json([
        'app' => 'DAYAMA PSB (Penerimaan Santri Baru)',
        'status' => 'operational',
    ]);
})->name('psb.home');

Route::get('/{institution}/register', function (string $institution) {
    return response()->json([
        'app' => 'DAYAMA PSB',
        'action' => 'register',
        'institution' => $institution,
    ]);
})->name('psb.register');
