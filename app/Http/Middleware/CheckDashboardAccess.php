<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->is_primary_super_admin && ! $request->user()->hasPermissionTo('dashboard.view')) {
            $blogDomain = config('platform.sites.blog.domain');
            $url = $request->getScheme() . '://' . $blogDomain . '/';

            return Inertia::location($url);
        }

        return $next($request);
    }
}
