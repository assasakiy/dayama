<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $host = $request->getSchemeAndHttpHost();

        $csp = [
            "default-src 'self' *.test-blog.test",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*.googletagmanager.com *.test-blog.test",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com *.test-blog.test",
            "img-src 'self' data: blob: http://localhost https://*.gravatar.com https://images.unsplash.com *.test-blog.test $host",
            "font-src 'self' https://fonts.gstatic.com data: *.test-blog.test",
            "connect-src 'self' https://*.google-analytics.com *.test-blog.test ws://localhost:* ws://127.0.0.1:* http://localhost:*",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
        ];

        // Matikan form-action self agar redirect Auth lintas domain dari Inertia tidak diblokir
        // $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
