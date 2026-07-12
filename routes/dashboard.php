<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Karena rute ini (Dashboard) nantinya akan dijalankan dari domain dashboard.contoh.com
// kita tidak memerlukan lagi prefix('dashboard') jika di mode multi-domain.
// Namun, jika di mode single, prefix tetap harus jalan.
// Logika single/multi akan ditangani oleh RouteServiceProvider / app.php

// Rute Dashboard Inti (Hanya bisa diakses jika sudah login DAN punya permission)
Route::middleware(['auth', 'dashboard.access'])->name('dashboard.')->group(function (): void {
    
    // Auth route untuk kemudahan logout dari dalam dashboard
    Route::post('/logout', [\App\Http\Controllers\Dashboard\AuthController::class, 'logout'])->name('logout');

    Route::get('/', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('index');
    Route::get('/posts', [\App\Http\Controllers\Dashboard\PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [\App\Http\Controllers\Dashboard\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [\App\Http\Controllers\Dashboard\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [\App\Http\Controllers\Dashboard\PostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{post}/revisions', [\App\Http\Controllers\Dashboard\PostController::class, 'revisions'])->name('posts.revisions');
    Route::post('/posts/{post}/restore-revision/{revision}', [\App\Http\Controllers\Dashboard\PostController::class, 'restoreRevision'])->name('posts.restore-revision');
    Route::patch('/posts/{post}/autosave', [\App\Http\Controllers\Dashboard\PostController::class, 'autosave'])->name('posts.autosave');
    Route::put('/posts/{post}', [\App\Http\Controllers\Dashboard\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/empty-trash', [\App\Http\Controllers\Dashboard\PostController::class, 'emptyTrash'])->name('posts.empty-trash');
    Route::delete('/posts/{post}', [\App\Http\Controllers\Dashboard\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{id}/restore', [\App\Http\Controllers\Dashboard\PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [\App\Http\Controllers\Dashboard\PostController::class, 'forceDelete'])->name('posts.force-delete');
    
    Route::get('/categories', [\App\Http\Controllers\Dashboard\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Dashboard\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\Dashboard\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Dashboard\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/tags', [\App\Http\Controllers\Dashboard\TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [\App\Http\Controllers\Dashboard\TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [\App\Http\Controllers\Dashboard\TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [\App\Http\Controllers\Dashboard\TagController::class, 'destroy'])->name('tags.destroy');
    
    Route::get('/comments', [\App\Http\Controllers\Dashboard\CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/status', [\App\Http\Controllers\Dashboard\CommentController::class, 'updateStatus'])->name('comments.update-status');
    Route::patch('/comments/{comment}/pin', [\App\Http\Controllers\Dashboard\CommentController::class, 'togglePin'])->name('comments.toggle-pin');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Dashboard\CommentController::class, 'destroy'])->name('comments.destroy');
    
    Route::get('/notifications', [\App\Http\Controllers\Dashboard\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Dashboard\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Dashboard\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    // Media
    Route::get('/media', [\App\Http\Controllers\Dashboard\MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [\App\Http\Controllers\Dashboard\MediaController::class, 'upload'])->name('media.upload');
    Route::get('/media/api/index', [\App\Http\Controllers\Dashboard\MediaController::class, 'apiIndex'])->name('media.api.index');

    // Settings
    Route::get('/settings/{group}', [\App\Http\Controllers\Dashboard\SettingController::class, 'show'])->name('settings.show');
    Route::put('/settings/{group}', [\App\Http\Controllers\Dashboard\SettingController::class, 'update'])->name('settings.update');

    // Account & User
    Route::get('/account/profile', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'profile'])->name('account.profile');
    Route::get('/account/security', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'security'])->name('account.security');
    
    Route::get('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'index'])->name('users.index');
});
