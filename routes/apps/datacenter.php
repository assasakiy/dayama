<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Data Center Application Routes (data.dayama.test)
|--------------------------------------------------------------------------
|
| Pusat master data manusia (Person Index), hubungan organisasi,
| serta cross-institution aggregation.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return response()->json([
            'app' => 'DAYAMA Data Center',
            'status' => 'operational',
            'scope' => 'foundation_registry',
        ]);
    })->name('datacenter.home');
});
