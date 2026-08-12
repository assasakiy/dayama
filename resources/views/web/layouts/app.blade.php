<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $context = $context ?? 'blog';
        $favicon = \App\Services\SettingService::get('general.favicon_url', null, $context);
    @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif

    {{-- SEO Meta --}}
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'A modern blog about technology, design, and development.')">
    @hasSection('keywords')
    <meta name="keywords" content="@yield('keywords')">
    @endif
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- OpenGraph --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', '')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('img/og-default.png'))">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', '')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('img/og-default.png'))">

    {{-- JSON-LD Schema --}}
    @stack('jsonld')

    {{-- Feeds --}}
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }}" href="{{ url('rss.xml') }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('sitemap.xml') }}">

    @vite(['resources/js/website.ts'])
    @stack('styles')

    @include('web.partials.theme-colors', ['context' => $context])
</head>
<body class="flex flex-col min-h-screen antialiased">
    <a href="#main-content" class="skip-link">{{ __('Skip to main content') }}</a>

    @include('web.partials.header.index', ['context' => $context])

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @include('web.partials.footer.index', ['context' => $context])

    @stack('scripts')

    <button
        x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => show = window.scrollY > 400)"
        x-show="show"
        x-cloak
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-50 w-10 h-10 rounded-full bg-primary text-primary-foreground shadow-elevated hover:bg-primary/90 transition-all flex items-center justify-center"
        aria-label="{{ __('Scroll to top') }}"
    >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>
    @livewireScripts
    <x-web.toast-manager />
    <x-web.cookie-banner />
    <x-web.cookie-preferences-modal />
</body>
</html>

