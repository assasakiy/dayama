@extends('web.layouts.app')
@section('title', __('About') . ' — ' . config('app.name'))
@section('content')
<section class="container-page py-12">
    <div class="max-w-[720px] mx-auto">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mb-6">{{ __('About') }}</h1>
        <div class="prose-blog">
            <p>{{ __('Welcome to :app — a modern blog about technology, design, and development.', ['app' => config('app.name')]) }}</p>
            <p>{{ __('We believe in sharing knowledge, exploring new ideas, and building a community of curious minds. Our articles cover a wide range of topics, from software engineering best practices to UI/UX design principles and everything in between.') }}</p>
            <p>{{ __('Whether you\'re a seasoned developer or just starting your journey, we hope you find something valuable here.') }}</p>
        </div>
    </div>
</section>
@endsection
