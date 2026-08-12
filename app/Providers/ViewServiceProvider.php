<?php

namespace App\Providers;

use Modules\Core\Models\Setting;
use App\Services\NavigationService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $navService = $this->app->make(NavigationService::class);

        View::composer('*', function ($view) use ($navService) {
            $view->with('siteSettings', SettingService::all());
            $view->with('navigation_categories', $navService->getCategories());

            $view->with('menuInstitutions', \Modules\Core\Models\Institution::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['name', 'slug']));
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
