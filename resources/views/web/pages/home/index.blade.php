@extends('web.layouts.app')

@section('title', $settings['site_name'] ?? 'Modern Blog')
@section('description', $settings['site_tagline'] ?? 'A modern blog about technology, design, and development.')
@section('og_type', 'website')

@section('content')
    <section class="container-page py-16 md:py-24">
        <div class="max-w-2xl mx-auto text-center">
            <span class="chip mb-4">{{ $settings['site_eyebrow'] ?? 'Stories for builders' }}</span>
            <h1 class="text-3xl md:text-5xl font-bold text-balance tracking-tight">{{ $settings['site_tagline'] ?? 'Where ideas take shape' }}</h1>
            <p class="mt-4 text-muted-foreground text-lg max-w-lg mx-auto text-balance">{{ $settings['site_description'] ?? 'Exploring technology, design, and the craft of building great software.' }}</p>
            <div class="flex items-center justify-center gap-3 mt-8">
                <a href="{{ route('blog.index') }}" class="btn btn-primary">Browse articles</a>
                <a href="{{ url('/about') }}" class="btn btn-outline">About us</a>
            </div>
        </div>
    </section>

    @if ($featured->count())
        <section class="container-page pb-12" aria-labelledby="featured-heading">
            <div class="flex items-center justify-between mb-8">
                <h2 id="featured-heading" class="text-lg font-semibold">Featured</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($featured as $index => $post)
                    <div class="{{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}">
                        @include('web.partials.post-card', ['post' => $post, 'featured' => $index === 0])
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($latest->count())
        <section class="container-page pb-12" aria-labelledby="latest-heading">
            <div class="flex items-center justify-between mb-8">
                <h2 id="latest-heading" class="text-lg font-semibold">Latest</h2>
                <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">View all</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($latest as $post)
                    @include('web.partials.post-card', ['post' => $post])
                @endforeach
            </div>
        </section>
    @endif

    @if ($categories->count())
        <section class="container-page pb-12" aria-labelledby="categories-heading">
            <div class="flex items-center justify-between mb-8">
                <h2 id="categories-heading" class="text-lg font-semibold">Topics</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($categories as $category)
                    <div class="card-surface p-6">
                        <h3 class="font-semibold mb-1">
                            <a href="{{ route('blog.category', $category->slug) }}" class="hover:text-primary transition-colors">{{ $category->name }}</a>
                        </h3>
                        @if ($category->description)
                            <p class="text-sm text-muted-foreground mb-3 truncate-2">{{ $category->description }}</p>
                        @endif
                        <span class="text-xs text-muted-foreground">{{ $category->posts_count }} {{ $category->posts_count === 1 ? 'article' : 'articles' }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-page pb-16">
        <div class="card-surface p-8 md:p-12 text-center">
            <h2 class="text-xl font-semibold mb-2">Stay in the loop</h2>
            <p class="text-muted-foreground text-sm mb-6 max-w-sm mx-auto">Get the latest posts delivered straight to your inbox. No spam, ever.</p>
            <div class="max-w-xs mx-auto">
                @include('web.partials.newsletter')
            </div>
        </div>
    </section>
@endsection
