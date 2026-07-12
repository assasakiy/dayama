<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Services\CrawlerService;

class EnsureVisitorToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip entirely for bots
        if (CrawlerService::isCrawler($request->userAgent())) {
            return $next($request);
        }

        $hasToken = $request->hasCookie('visitor_token');
        $token = $request->cookie('visitor_token') ?? (string) Str::uuid();

        // If the token wasn't in the request, we add it to the request so it's available immediately
        if (! $hasToken) {
            $request->cookies->set('visitor_token', $token);
        }

        $response = $next($request);

        if (! $hasToken && method_exists($response, 'withCookie')) {
            // Store for 1 year (525600 minutes)
            $response->withCookie(cookie('visitor_token', $token, 525600));
        }

        return $response;
    }
}

