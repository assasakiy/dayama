<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $favicon = \App\Services\SettingService::get('general.favicon_url', null, 'global');
        $primaryColor = \App\Services\SettingService::get('appearance.primary_color', null, 'global');
        $secondaryColor = \App\Services\SettingService::get('appearance.secondary_color', null, 'global');
        $siteName = \App\Services\SettingService::get('general.site_name', config('app.name', 'Modern Blog'), 'global');
    @endphp
    <title>@yield('title', 'Error') - {{ $siteName }}</title>
    <meta name="robots" content="noindex, nofollow">
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
    @if($primaryColor || $secondaryColor)
    <style>
        :root, .dark {
            @if($primaryColor) --color-primary: {{ $primaryColor }}; @endif
            @if($secondaryColor) --color-secondary: {{ $secondaryColor }}; @endif
        }
    </style>
    @endif
    @vite(['resources/js/website.ts'])
</head>
<body class="flex flex-col min-h-screen antialiased bg-background text-foreground items-center justify-center p-6">
    <div class="w-full max-w-md text-center space-y-6">
        <div class="text-[8rem] font-black leading-none text-primary/10 select-none">
            @yield('code', '500')
        </div>
        
        <div class="space-y-2 relative z-10 -mt-16">
            <h1 class="text-2xl font-bold tracking-tight">@yield('message', 'Something went wrong.')</h1>
            <p class="text-muted-foreground">@yield('description', 'An unexpected error occurred. Please try again later.')</p>
        </div>

        <div class="pt-6 flex items-center justify-center gap-4">
            <a href="{{ url('/') }}" target="_top" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-md bg-primary text-primary-foreground text-sm font-medium transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                {{ __('Go Home') }}
            </a>
            <button onclick="window.top.location.reload()" type="button" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-md border border-border-subtle bg-background text-foreground text-sm font-medium transition-colors hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                {{ __('Try Again') }}
            </button>
        </div>
    </div>
</body>
</html>
