<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Rute Dashboard Inti (Hanya bisa diakses jika sudah login DAN punya permission)
Route::middleware(['auth', 'dashboard.access'])->name('dashboard.')->group(function (): void {

    Route::post('/logout', [\App\Http\Controllers\Dashboard\AuthController::class, 'logout'])->name('logout');

    Route::get('/', [\App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('index');

    // ─── Posts ────────────────────────────────────────────────
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

    // ─── Categories ───────────────────────────────────────────
    Route::get('/categories', [\App\Http\Controllers\Dashboard\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Dashboard\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\Dashboard\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Dashboard\CategoryController::class, 'destroy'])->name('categories.destroy');

    // ─── Tags ─────────────────────────────────────────────────
    Route::get('/tags', [\App\Http\Controllers\Dashboard\TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [\App\Http\Controllers\Dashboard\TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [\App\Http\Controllers\Dashboard\TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [\App\Http\Controllers\Dashboard\TagController::class, 'destroy'])->name('tags.destroy');

    // ─── Comments ─────────────────────────────────────────────
    Route::get('/comments', [\App\Http\Controllers\Dashboard\CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/status', [\App\Http\Controllers\Dashboard\CommentController::class, 'updateStatus'])->name('comments.update-status');
    Route::patch('/comments/{comment}/pin', [\App\Http\Controllers\Dashboard\CommentController::class, 'togglePin'])->name('comments.toggle-pin');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Dashboard\CommentController::class, 'destroy'])->name('comments.destroy');

    // ─── Notifications ────────────────────────────────────────
    Route::get('/notifications', [\App\Http\Controllers\Dashboard\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Dashboard\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Dashboard\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Dashboard\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // ─── Media ─────────────────────────────────────────────────
    Route::get('/media', [\App\Http\Controllers\Dashboard\MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [\App\Http\Controllers\Dashboard\MediaController::class, 'upload'])->name('media.upload');
    Route::post('/media', [\App\Http\Controllers\Dashboard\MediaController::class, 'store'])->name('media.store');
    Route::get('/media/api/index', [\App\Http\Controllers\Dashboard\MediaController::class, 'apiIndex'])->name('media.api.index');
    Route::put('/media/{medium}', [\App\Http\Controllers\Dashboard\MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{medium}', [\App\Http\Controllers\Dashboard\MediaController::class, 'destroy'])->name('media.destroy');

    // ─── Settings ──────────────────────────────────────────────
    Route::get('/settings/{context}/{group?}', [\App\Http\Controllers\Dashboard\SettingController::class, 'show'])->name('settings.show');
    Route::put('/settings/{context}/{group?}', [\App\Http\Controllers\Dashboard\SettingController::class, 'update'])->name('settings.update');

    // ─── Account ───────────────────────────────────────────────
    Route::prefix('account')->name('account.')->group(function (): void {
        Route::get('/', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'index'])->name('index');
        Route::get('/details', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'index'])->name('details');
        Route::put('/', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'update'])->name('update');

        // Emails
        Route::post('/details/emails', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'storeEmail'])->name('emails.store');
        Route::delete('/details/emails/{id}', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'destroyEmail'])->name('emails.destroy');
        Route::put('/details/emails/{id}/primary', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'setPrimaryEmail'])->name('emails.set-primary');
        Route::post('/details/emails/{id}/resend', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'resendVerification'])->name('emails.resend');
        Route::post('/details/emails/{id}/verify', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'verifyEmailCode'])->name('emails.verify');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Dashboard\Account\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Dashboard\Account\ProfileController::class, 'update'])->name('profile.update');

        // Security
        Route::get('/security', [\App\Http\Controllers\Dashboard\Account\SecurityController::class, 'index'])->name('security');
        Route::put('/security/password', [\App\Http\Controllers\Dashboard\Account\SecurityController::class, 'updatePassword'])->name('security.password');
        Route::post('/security/two-factor', [\App\Http\Controllers\Dashboard\Account\SecurityController::class, 'enableTwoFactor'])->name('security.two-factor.enable');
        Route::post('/security/two-factor/confirm', [\App\Http\Controllers\Dashboard\Account\SecurityController::class, 'confirmTwoFactor'])->name('security.two-factor.confirm');
        Route::delete('/security/two-factor', [\App\Http\Controllers\Dashboard\Account\SecurityController::class, 'disableTwoFactor'])->name('security.two-factor.disable');

        // Appearance
        Route::get('/appearance', [\App\Http\Controllers\Dashboard\Account\AppearanceController::class, 'index'])->name('appearance');
        Route::put('/appearance', [\App\Http\Controllers\Dashboard\Account\AppearanceController::class, 'update'])->name('appearance.update');

        // Connected Accounts
        Route::get('/connected', [\App\Http\Controllers\Dashboard\Account\ConnectedAccountController::class, 'index'])->name('connected');
        Route::get('/connected/{provider}/redirect', [\App\Http\Controllers\Dashboard\Account\ConnectedAccountController::class, 'redirect'])->name('connected.redirect');
        Route::get('/connected/{provider}/callback', [\App\Http\Controllers\Dashboard\Account\ConnectedAccountController::class, 'callback'])->name('connected.callback');
        Route::delete('/connected/{id}', [\App\Http\Controllers\Dashboard\Account\ConnectedAccountController::class, 'destroy'])->name('connected.destroy');

        // Delete Account
        Route::get('/delete', [\App\Http\Controllers\Dashboard\Account\DeleteAccountController::class, 'index'])->name('delete');
        Route::delete('/delete', [\App\Http\Controllers\Dashboard\Account\DeleteAccountController::class, 'destroy'])->name('delete.destroy');

        // Export Data
        Route::get('/export', [\App\Http\Controllers\Dashboard\Account\ExportDataController::class, 'index'])->name('export');
        Route::get('/export/download', [\App\Http\Controllers\Dashboard\Account\ExportDataController::class, 'export'])->name('export.download');

        // Notifications (account preferences)
        Route::get('/notifications', [\App\Http\Controllers\Dashboard\Account\NotificationController::class, 'index'])->name('notifications');
        Route::put('/notifications', [\App\Http\Controllers\Dashboard\Account\NotificationController::class, 'update'])->name('notifications.update');

        // Writing
        Route::get('/writing', [\App\Http\Controllers\Dashboard\Account\WritingController::class, 'index'])->name('writing');
        Route::put('/writing', [\App\Http\Controllers\Dashboard\Account\WritingController::class, 'update'])->name('writing.update');
    });

    // ─── Users ─────────────────────────────────────────────────
    Route::get('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'show'])->name('users.show');
    Route::post('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Dashboard\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Dashboard\UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/users', [\App\Http\Controllers\Dashboard\UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('/users/bulk-role', [\App\Http\Controllers\Dashboard\UserController::class, 'bulkAssignRole'])->name('users.bulk-role');

    // ─── Roles ─────────────────────────────────────────────────
    Route::get('/roles', [\App\Http\Controllers\Dashboard\RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [\App\Http\Controllers\Dashboard\RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [\App\Http\Controllers\Dashboard\RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [\App\Http\Controllers\Dashboard\RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{role}/duplicate', [\App\Http\Controllers\Dashboard\RoleController::class, 'duplicate'])->name('roles.duplicate');
    Route::post('/roles/{role}/assign-users', [\App\Http\Controllers\Dashboard\RoleController::class, 'assignUsers'])->name('roles.assign-users');

    // ─── Permissions ───────────────────────────────────────────
    Route::get('/permissions', [\App\Http\Controllers\Dashboard\PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [\App\Http\Controllers\Dashboard\PermissionController::class, 'store'])->name('permissions.store');
    Route::put('/permissions/{permission}', [\App\Http\Controllers\Dashboard\PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [\App\Http\Controllers\Dashboard\PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('/permissions/seed', [\App\Http\Controllers\Dashboard\PermissionController::class, 'seed'])->name('permissions.seed');

    // ─── Permission Groups ─────────────────────────────────────
    Route::get('/permission-groups', [\App\Http\Controllers\Dashboard\PermissionGroupController::class, 'index'])->name('permission-groups.index');
    Route::post('/permission-groups', [\App\Http\Controllers\Dashboard\PermissionGroupController::class, 'store'])->name('permission-groups.store');
    Route::put('/permission-groups/{permissionGroup}', [\App\Http\Controllers\Dashboard\PermissionGroupController::class, 'update'])->name('permission-groups.update');
    Route::delete('/permission-groups/{permissionGroup}', [\App\Http\Controllers\Dashboard\PermissionGroupController::class, 'destroy'])->name('permission-groups.destroy');

    // ─── Email Templates ───────────────────────────────────────
    Route::get('/email-templates', [\App\Http\Controllers\Dashboard\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::get('/email-templates/{emailTemplate}/edit', [\App\Http\Controllers\Dashboard\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
    Route::put('/email-templates/{emailTemplate}', [\App\Http\Controllers\Dashboard\EmailTemplateController::class, 'update'])->name('email-templates.update');
    Route::get('/email-templates/{emailTemplate}/preview', [\App\Http\Controllers\Dashboard\EmailTemplateController::class, 'preview'])->name('email-templates.preview');

    // ─── Activity Logs ─────────────────────────────────────────
    Route::get('/activity-logs', [\App\Http\Controllers\Dashboard\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::delete('/activity-logs/{id}', [\App\Http\Controllers\Dashboard\ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    Route::delete('/activity-logs', [\App\Http\Controllers\Dashboard\ActivityLogController::class, 'bulkDelete'])->name('activity-logs.bulk-delete');

    // ─── Bookmarks & History ──────────────────────────────────
    Route::get('/bookmarks', [\App\Http\Controllers\Dashboard\User\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::delete('/bookmarks/{postId}', [\App\Http\Controllers\Dashboard\User\BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::get('/history', [\App\Http\Controllers\Dashboard\User\ReadingHistoryController::class, 'index'])->name('history.index');
});
