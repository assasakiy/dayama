@extends('web.layouts.app')

@section('title', config('app.name') . ' - Homepage')
@section('description', 'Welcome to the main landing page.')

@section('content')
<div class="container-page py-16 md:py-24 text-center">
    <h1 class="text-4xl font-extrabold mb-4 text-foreground tracking-tight">Selamat Datang di {{ config('app.name') }}</h1>
    <p class="text-lg text-muted-foreground mb-8 leading-relaxed max-w-2xl mx-auto">
        Ini adalah halaman pendaratan (Landing Page) utama. Arsitektur Multi-Domain berbasis file telah sukses diimplementasikan.
    </p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="http://{{ config('projects.domains.blog', env('DOMAIN_BLOG', 'blog.test-blog.test')) }}" class="btn btn-primary">
            Kunjungi Blog
        </a>
        <a href="http://{{ config('projects.domains.auth', env('DOMAIN_AUTH', 'account.test-blog.test')) }}/login" class="btn btn-outline">
            Login / Dashboard
        </a>
    </div>
</div>
@endsection
