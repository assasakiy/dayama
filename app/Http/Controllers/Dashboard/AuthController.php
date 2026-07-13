<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController
{
    public function register(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): SymfonyResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        Auth::login($user);

        // User yang baru register biasanya adalah user reguler
        $blogDomain = config('projects.projects.blog.domain');
        $url = $request->getScheme() . '://' . $blogDomain . '/';

        return Inertia::location($url);
    }

    public function login(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request): SymfonyResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->hasRole('super-admin') || $user->hasPermissionTo('dashboard.view')) {
                $dashboardDomain = config('projects.core.dashboard');
                $url = $request->getScheme() . '://' . $dashboardDomain . '/';

                return Inertia::location($url);
            }

            $blogDomain = config('projects.projects.blog.domain');
            $url = $request->getScheme() . '://' . $blogDomain . '/';

            return Inertia::location($url);
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request): SymfonyResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $authDomain = config('projects.core.auth');
        $url = $request->getScheme() . '://' . $authDomain . '/login';

        return Inertia::location($url);
    }
}
