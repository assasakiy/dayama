<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-screen w-screen overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name') }} — Dashboard</title>
    @php
        $favicon = \App\Services\SettingService::get('general.favicon_url', null, 'global');
        $primaryColor = \App\Services\SettingService::get('appearance.primary_color', null, 'global');
        $secondaryColor = \App\Services\SettingService::get('appearance.secondary_color', null, 'global');
    @endphp
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
    @vite(['resources/js/dashboard/main.tsx'])
    @inertiaHead
</head>
<body class="antialiased bg-[oklch(0.97_0_0)] h-screen w-screen overflow-hidden m-0 p-0">
    @inertia
</body>
</html>
