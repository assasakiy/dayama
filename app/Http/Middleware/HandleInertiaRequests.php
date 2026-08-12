<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function rootView(Request $request): string
    {
        // Gunakan host base atau request path untuk menentukan view mana yang akan di load
        $isDashboardDomain = $request->getHost() === config('projects.core.dashboard');
        $isAuthDomain = $request->getHost() === config('projects.core.auth');

        if ($isDashboardDomain || $isAuthDomain || $request->is('dashboard*') || $request->is('login*') || $request->is('register*')) {
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
        // Tentukan context branding berdasarkan domain
        $brandingContext = 'global';
        if ($request->getHost() === config('projects.projects.blog.domain')) {
            $brandingContext = 'blog';
        } elseif ($request->getHost() === config('projects.projects.landing.domain')) {
            $brandingContext = 'landing';
        }

        $user = $request->user();
        $activeInstitutionId = session('active_institution_id');

        $institutions = $user
            ? \Modules\Core\Models\Institution::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'logo_url'])
            : collect();

        $activeInstitution = $activeInstitutionId
            ? $institutions->firstWhere('id', $activeInstitutionId)
            : null;

        return [
            ...parent::share($request),
            'blog_url' => ($request->secure() ? 'https://' : 'http://') . config('projects.projects.blog.domain'),
            'domain_main' => config('projects.projects.landing.domain'),
            'csrf_token' => csrf_token(),
            'active_institution' => $activeInstitution,
            'institutions' => $institutions,
            'auth' => [
                'user' => $user ? array_merge(
                    $user->load(['roles'])->loadCount('posts')->append(['name', 'avatar_url', 'banner_url', 'biography', 'website', 'social_links', 'avatar_media', 'banner_media'])->toArray(),
                    [
                        'highest_rank' => $user->getHighestRank(),
                        'is_primary_super_admin' => $user->is_primary_super_admin,
                        'is_protected' => $user->is_protected,
                        'is_verified' => $user->is_verified,
                        'unread_notifications' => $user->unreadNotifications()->take(5)->get(),
                        'unread_notifications_count' => $user->unreadNotifications()->count(),
                    ]
                ) : null,
                'roles' => $user ? $user->getRoleNames() : [],
                'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
            ],
            'flash' => [
                'toast' => fn () => $request->session()->get('toast'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
                'status' => fn () => $request->session()->get('status'),
            ],
            'settings' => [
                'general' => \App\Services\SettingService::group('general', $brandingContext),
                'appearance' => \App\Services\SettingService::group('appearance', 'dashboard'),
            ],
            'landing_pages' => \Modules\Landing\Models\Page::orderBy('sort_order')->get(['id', 'name', 'slug']),
        ];
    }
}
