<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EtagMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $content = $response->getContent();

        if ($response->isSuccessful() && is_string($content) && $content !== '') {
            $etag = sprintf('W/"%s"', crc32($content));
            $response->headers->set('ETag', $etag);

            if ($request->header('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent(null);
            }
        }

        return $response;
    }
}
