<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function rootView(Request $request): string
    {
        if ($request->is('dashboard*') || $request->is('login*') || $request->is('register*')) {
            return 'dashboard';
        }

        return 'app';
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $request->user() ? array_merge(
                    $request->user()->load(['roles'])->loadCount('posts')->append(['avatar_url', 'banner_url', 'biography', 'website', 'social_links', 'avatar_media', 'banner_media'])->toArray(),
                    [
                        'highest_rank' => $request->user()->getHighestRank(),
                        'is_primary_super_admin' => $request->user()->is_primary_super_admin,
                        'is_protected' => $request->user()->is_protected,
                        'is_verified' => $request->user()->is_verified,
                        'unread_notifications' => $request->user()->unreadNotifications()->take(5)->get(),
                        'unread_notifications_count' => $request->user()->unreadNotifications()->count(),
                    ]
                ) : null,
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
            ],
            'flash' => [
                'toast' => fn () => $request->session()->get('toast'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
