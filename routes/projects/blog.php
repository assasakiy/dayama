<?php

declare(strict_types=1);

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\TagController;
use App\Http\Controllers\Web\AuthorController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\RssController;
use Illuminate\Support\Facades\Route;

// Public website routes (Sekarang ini adalah Blog di blog.test-blog.test)
// Karena ini sudah domain khusus blog, kita tidak butuh LandingController lagi di sini.
// Route::get('/', [\App\Http\Controllers\Web\LandingController::class, 'index'])->name('home');

// Rute Utama Blog (Home)
Route::get('/', [\App\Http\Controllers\Web\HomeController::class, '__invoke'])->name('home');

Route::post('/cookie-consent', [\App\Http\Controllers\Web\CookieConsentController::class, 'store'])->name('cookie-consent.store');

Route::prefix('post')->group(function (): void {
    // Post Archive (Indeks Artikel)
    Route::get('/', [BlogController::class, '__invoke'])->name('blog.index');
    Route::get('/trending', [BlogController::class, 'trending'])->name('blog.trending');
    Route::get('/{post:slug}', [PostController::class, 'show'])->name('blog.show');
    Route::put('/{post:slug}/reaction', [\App\Http\Controllers\Web\ReactionController::class, 'update'])->name('blog.reaction');
    Route::put('/{post:slug}/bookmark', [\App\Http\Controllers\Web\BookmarkController::class, 'update'])->name('blog.bookmark');
});

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');

Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag.show');
Route::get('/author/{user:username}', [AuthorController::class, 'show'])->name('author.show');

Route::get('/search', SearchController::class)->name('search');

Route::post('/comments', [\App\Http\Controllers\Web\CommentController::class, 'store'])->name('comments.store');
Route::post('/notifications/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('notifications.read.all')->middleware('auth');

// SEO feeds
Route::get('/sitemap.xml', [SitemapController::class, '__invoke'])->name('sitemap');

Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-tags.xml', [SitemapController::class, 'tags'])->name('sitemap.tags');
Route::get('/rss.xml', [RssController::class, '__invoke'])->name('rss');

Route::get('/verify-email/{id}/{hash}', [\App\Http\Controllers\Dashboard\Account\AccountController::class, 'verifyEmail'])->name('verification.verify.custom');
