<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Di mode multi-domain (api.contoh.com), kita seringkali tidak membutuhkan
// prefix /v1 lagi, tetapi untuk kompatibilitas bisa kita pertahankan, atau
// membiarkan logika ini di-handle oleh app.php (bisa tambah prefix di sana jika perlu).
// Di sini kita biarkan prefix v1 karena cukup lazim untuk struktur API.

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name') . ' API',
        'message' => 'API is running',
        'version' => '1.0.0',
        'status' => 'healthy'
    ]);
});

Route::prefix('v1')->name('api.v1.')->group(function (): void {

    // Auth
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);

    // Public resources
    Route::get('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'index']);
    Route::get('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'show']);
    Route::get('/categories', [\App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
    Route::get('/categories/{category}', [\App\Http\Controllers\Api\V1\CategoryController::class, 'show']);
    Route::get('/tags', [\App\Http\Controllers\Api\V1\TagController::class, 'index']);
    Route::get('/tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'show']);

    // Protected resources
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'store']);
        Route::put('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'update']);
        Route::delete('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'destroy']);

        Route::post('/categories', [\App\Http\Controllers\Api\V1\CategoryController::class, 'store']);
        Route::put('/categories/{category}', [\App\Http\Controllers\Api\V1\CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [\App\Http\Controllers\Api\V1\CategoryController::class, 'destroy']);

        Route::post('/tags', [\App\Http\Controllers\Api\V1\TagController::class, 'store']);
        Route::put('/tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'update']);
        Route::delete('/tags/{tag}', [\App\Http\Controllers\Api\V1\TagController::class, 'destroy']);

        Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/user', [\App\Http\Controllers\Api\V1\AuthController::class, 'user']);
    });
});
