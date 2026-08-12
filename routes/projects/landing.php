<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Projects\Landing\LandingController;

// Rute untuk domain utama (Landing Page Utama Dayama)
Route::get('/', [LandingController::class, 'index'])->name('landing.home');

// Rute Dinamis untuk Halaman Profil, Pendidikan, Layanan, Media
// Ini akan mencakup pola seperti /profil/sejarah atau /layanan/psb
Route::get('/{section}/{page}', [LandingController::class, 'page'])
    ->where('section', 'profil|pendidikan|layanan|media')
    ->where('page', '.*')
    ->name('landing.page');

// Sitemap khusus landing page
Route::get('/sitemap.xml', [\App\Http\Controllers\Web\SitemapController::class, 'landingIndex'])->name('landing.sitemap');
Route::get('/sitemap-landing.xsl', [\App\Http\Controllers\Web\SitemapController::class, 'landingXsl'])->name('landing.sitemap.xsl');
Route::get('/sitemap-{section}.xml', [\App\Http\Controllers\Web\SitemapController::class, 'landingSection'])
    ->where('section', 'profil|pendidikan|layanan|media')
    ->name('landing.sitemap.section');
