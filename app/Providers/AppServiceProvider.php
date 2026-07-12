<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \App\Toast\Contracts\ToastManagerInterface::class,
            \App\Toast\SessionToastManager::class
        );
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Register custom policies for third-party models
        // Register custom policies for third-party models
        Gate::policy(\Spatie\MediaLibrary\MediaCollections\Models\Media::class, \App\Policies\MediaPolicy::class);

        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Post::observe(\App\Observers\PostObserver::class);
        \App\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \App\Models\Tag::observe(\App\Observers\TagObserver::class);
        \App\Models\PostView::observe(\App\Observers\PostViewObserver::class);
        \App\Models\Reaction::observe(\App\Observers\ReactionObserver::class);

        Event::listen(Login::class, \App\Listeners\MigrateGuestDataToUser::class);
        Event::listen(Login::class, function (Login $event) {
            activity('auth')
                ->causedBy($event->user)
                ->event('login')
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('User logged in');
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                activity('auth')
                    ->causedBy($event->user)
                    ->event('logout')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                    ->log('User logged out');
            }
        });

        Event::listen(\Illuminate\Auth\Events\Registered::class, function (\Illuminate\Auth\Events\Registered $event) {
            activity('auth')
                ->causedBy($event->user)
                ->event('registered')
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent()])
                ->log('New user registered');
        });

        // Permissions are now strictly enforced dynamically via roles and policies
    }
}
