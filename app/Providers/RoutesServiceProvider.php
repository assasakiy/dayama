<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RoutesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapPlatformApps();
        $this->mapPlatformSites();
    }

    /**
     * Memuat rute untuk Platform Applications (Account, Dashboard, Portal, PSB, dll)
     */
    protected function mapPlatformApps(): void
    {
        $apps = config('platform.apps', []);

        foreach ($apps as $key => $app) {
            if (! empty($app['enabled']) && ! empty($app['domain']) && ! empty($app['route_file'])) {
                $filePath = base_path($app['route_file']);
                if (file_exists($filePath)) {
                    Route::domain($app['domain'])
                        ->middleware($app['middleware'] ?? ['web'])
                        ->group($filePath);
                }
            }
        }
    }

    /**
     * Memuat rute untuk Sites & Content Surfaces (Landing Yayasan, Blog, dll)
     */
    protected function mapPlatformSites(): void
    {
        $sites = config('platform.sites', []);

        foreach ($sites as $key => $site) {
            if (! empty($site['enabled']) && ! empty($site['domain']) && ! empty($site['route_file'])) {
                $filePath = base_path($site['route_file']);
                if (file_exists($filePath)) {
                    Route::domain($site['domain'])
                        ->middleware($site['middleware'] ?? ['web'])
                        ->group($filePath);
                }
            }
        }
    }
}

