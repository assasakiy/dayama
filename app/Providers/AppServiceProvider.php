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

        // Primary Super Admin bypass — hanya user dengan is_primary_super_admin=true yang bypass
        // Semua user lain (termasuk yg punya role super-admin) harus melalui permission system
        Gate::before(function ($user, $ability) {
            return $user->is_primary_super_admin ? true : null;
        });

        // Register custom policies for third-party models
        Gate::policy(\Modules\Core\Models\Media::class, \App\Policies\MediaPolicy::class);

        // Tell Laravel how to discover policies for models located in the Modules folder
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            if (str_starts_with($modelClass, 'Modules\\')) {
                // Modules\Core\Models\User -> App\Policies\UserPolicy
                $classBasename = class_basename($modelClass);
                return 'App\\Policies\\' . $classBasename . 'Policy';
            }
            // Fallback for models inside app/Models
            return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        });

        \Modules\Core\Models\User::observe(\App\Observers\UserObserver::class);
        \Modules\CMS\Models\Post::observe(\App\Observers\PostObserver::class);
        \Modules\CMS\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \Modules\CMS\Models\Tag::observe(\App\Observers\TagObserver::class);
        \Modules\CMS\Models\PostView::observe(\App\Observers\PostViewObserver::class);
        \Modules\CMS\Models\Reaction::observe(\App\Observers\ReactionObserver::class);
        \Modules\Core\Models\Person::observe(\App\Observers\PersonObserver::class);

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
