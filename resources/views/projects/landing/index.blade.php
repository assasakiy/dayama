<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Homepage</title>
    
    <!-- Untuk saat ini kita pakai CDN Tailwind agar tidak error Vite -->
    <!-- Jika nanti Anda membuat desain khusus, Anda bisa tambahkan file CSS/JS ke vite.config.ts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans flex items-center justify-center min-h-screen">
    <div class="text-center p-8 bg-white shadow-xl rounded-2xl max-w-2xl border border-gray-100">
        <div class="mb-6 flex justify-center">
            <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold mb-4 text-gray-900 tracking-tight">Selamat Datang di {{ config('app.name') }}</h1>
        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            Ini adalah halaman pendaratan (Landing Page) utama. Arsitektur Multi-Domain berbasis file telah sukses diimplementasikan.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="http://{{ config('projects.domains.blog') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition duration-200">
                Kunjungi Blog
            </a>
            <a href="http://{{ config('projects.domains.auth') }}/login" class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 shadow-sm rounded-lg font-semibold transition duration-200">
                Login / Dashboard
            </a>
        </div>
    </div>
</body>
</html>
