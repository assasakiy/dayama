<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name') }}</title>
    @php
        $favicon = \App\Services\SettingService::get('general.favicon_url', null, 'global');
        $primaryColor = \App\Services\SettingService::get('appearance.primary_color', null, 'global');
        $secondaryColor = \App\Services\SettingService::get('appearance.secondary_color', null, 'global');
        $accentColor = \App\Services\SettingService::get('appearance.accent_color', null, 'global');
    @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
    @if($primaryColor || $secondaryColor)
    <style>
        :root, .dark {
            @if($primaryColor) --color-primary: {{ $primaryColor }}; @endif
            @if($secondaryColor) --color-secondary: {{ $secondaryColor }}; @endif
            @if($accentColor) --color-accent: {{ $accentColor }}; @endif
        }
    </style>
    @endif
    @vite(['resources/js/website.ts'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>
