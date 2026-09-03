<?php

declare(strict_types=1);

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\EtagMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    // Menggunakan using() agar pendaftaran routing diserahkan 
    // secara penuh ke RoutesServiceProvider untuk menangani Multi-Domain
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            // Kita delegasikan ini ke RoutesServiceProvider.php 
            // yang sudah di-register otomatis oleh Laravel atau lewat config/app.php
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureVisitorToken::class,
            HandleInertiaRequests::class,
            SecurityHeaders::class,
            ContentSecurityPolicy::class,
            EtagMiddleware::class,
        ]);

        $middleware->api(append: [
            SecurityHeaders::class,
            ContentSecurityPolicy::class,
        ]);

        // Mendaftarkan alias middleware agar bisa dipanggil di rute
        $middleware->alias([
            'dashboard.access' => \App\Http\Middleware\CheckDashboardAccess::class,
            'institution.scope' => \App\Http\Middleware\CheckInstitutionScope::class,
        ]);

        // Kustomisasi pengalihan Guest ke domain Auth
        $middleware->redirectGuestsTo(function (Request $request) {
            $authDomain = config('platform.apps.account.domain', 'account.' . config('platform.root_domain'));
            return $request->getScheme() . '://' . $authDomain . '/login';
        });

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->getHost() === config('platform.apps.api.domain'),
        );

        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $isDashboard = $request->getHost() === config('platform.apps.dashboard.domain') || $request->is('dashboard*');
            
            if ($request->header('X-Inertia') || $isDashboard) {
                if (in_array($response->getStatusCode(), [403, 404, 500, 503])) {
                    if (! app()->environment(['local', 'testing']) || in_array($response->getStatusCode(), [403, 404])) {
                        return Inertia::render('Error', ['status' => $response->getStatusCode()])
                            ->toResponse($request)
                            ->setStatusCode($response->getStatusCode());
                    }
                }
            }
            return $response;
        });
    })->create();
