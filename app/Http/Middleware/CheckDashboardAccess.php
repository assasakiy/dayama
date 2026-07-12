<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login (middleware 'auth' biasanya dipanggil sebelum ini)
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek permission menggunakan fitur dari spatie/laravel-permission
        if (! $request->user()->hasRole('super-admin') && ! $request->user()->hasPermissionTo('access dashboard')) {
            // Jika user tidak punya akses, tendang kembali ke blog (karena kita pakai pure multi domain)
            $blogDomain = config('projects.projects.blog.domain');
            return redirect()->to($request->getScheme() . '://' . $blogDomain . '/')->with('error', 'You do not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
