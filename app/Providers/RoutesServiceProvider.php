<?php

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
        $this->mapCoreRoutes();
        $this->mapProjectRoutes();
    }

    /**
     * Memuat rute untuk Sistem Inti (API, Auth, Dashboard)
     */
    protected function mapCoreRoutes(): void
    {
        // 1. Domain API
        if ($domain = config('projects.core.api')) {
            Route::domain($domain)
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        }

        // 2. Domain Auth (Account)
        if ($domain = config('projects.core.auth')) {
            Route::domain($domain)
                ->middleware('web')
                ->group(base_path('routes/auth.php'));
        }

        // 3. Domain Dashboard
        if ($domain = config('projects.core.dashboard')) {
            Route::domain($domain)
                ->middleware(['web'])
                ->group(base_path('routes/dashboard.php'));
        }
    }

    /**
     * Memuat rute untuk Frontends/Projects (Blog, Landing, dll)
     */
    protected function mapProjectRoutes(): void
    {
        $projects = config('projects.projects', []);
        
        foreach ($projects as $name => $project) {
            if (isset($project['active']) && $project['active'] === true) {
                Route::domain($project['domain'])
                    ->middleware('web') // Default middleware untuk project frontend
                    ->group(base_path($project['route_file']));
            }
        }
    }
}
