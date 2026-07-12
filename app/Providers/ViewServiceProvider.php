<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\NavigationService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $settingService = $this->app->make(SettingService::class);
        $navService = $this->app->make(NavigationService::class);

        View::composer('*', function ($view) use ($settingService, $navService) {
            $view->with('siteSettings', $settingService->getSnapshot());
            $view->with('navigation_categories', $navService->getCategories());
        });

        View::composer('web.layouts.app', function ($view) {
            $theme = request()->cookie('theme', 'light');
            $view->with('defaultTheme', $theme);
        });
    }

    public function register(): void
    {
        //
    }
}
