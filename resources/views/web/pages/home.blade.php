@extends('web.layouts.app')

@section('title', config('app.name') . ' — A Modern Blog')
@section('description', 'Discover articles about technology, design, development, and more.')
@section('og_title', config('app.name'))

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden border-b border-border-subtle bg-gradient-to-b from-surface-muted/40 to-background">
    <div class="container-page py-16 md:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-primary-muted text-primary text-xs font-medium mb-6 border border-primary-border">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse shrink-0"></span>
                    <span>{{ __('Latest insights') }}</span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-balance leading-[1.1]">
                    {{ __('Exploring the future of') }}<br>
                    <span class="text-primary">{{ __('technology') }}</span> {{ __('and') }} <span class="text-primary">{{ __('design') }}</span>.
                </h1>
                <p class="mt-4 text-lg text-muted-foreground text-pretty max-w-lg">
                    {{ __('Stories, insights, and tutorials from the intersection of engineering and creativity.') }}
                </p>
                <div class="flex items-center gap-3 mt-8">
                    <a href="{{ route('blog.index') }}" class="btn btn-primary">{{ __('Browse Articles') }}</a>
                    <a href="{{ url('/about') }}" class="btn btn-outline">{{ __('About Us') }}</a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-gradient-to-br from-primary/10 via-primary-muted to-background border border-border-subtle shadow-elevated">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50" />
                    <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-primary/10 blur-3xl" />
                    <div class="absolute -bottom-10 -left-10 w-48 h-48 rounded-full bg-primary/5 blur-2xl" />
                    <div class="absolute inset-0 flex items-center justify-center p-8">
                        <div class="grid grid-cols-3 gap-3 w-full max-w-sm">
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F4BB;</div>
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F3A8;</div>
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F4E1;</div>
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F916;</div>
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F4DD;</div>
                            <div class="aspect-square rounded-xl bg-background/60 backdrop-blur-sm border border-border-subtle flex items-center justify-center text-2xl">&#x1F4AC;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Posts --}}
@if($featured->isNotEmpty())
<section class="container-page pb-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-border-subtle rounded-md overflow-hidden border border-border-subtle">
        @foreach($featured->take(2) as $post)
        <div class="bg-background p-6 md:p-8 h-full">
            <x-web.partials.post-card :post="$post" class="[&_h3]:text-xl" />
        </div>
        @endforeach
        @if($featured->count() > 2)
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-border-subtle border-t md:border-t-0 border-border-subtle">
            @foreach($featured->skip(2) as $post)
            <div class="bg-background p-6 h-full">
                <x-web.partials.post-card :post="$post" />
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

{{-- Categories --}}
<section class="container-page pb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold">{{ __('Topics') }}</h2>
        <a href="{{ route('categories.index') }}" class="btn btn-ghost text-sm">{{ __('View all') }} &rarr;</a>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach($categories as $category)
        <a href="{{ route('category.show', $category) }}"
           class="chip text-sm px-3 py-1.5 hover:bg-surface-muted hover:text-foreground transition-colors no-underline">
            {{ $category->name }}
            <span class="text-xs text-muted-foreground ml-1">({{ $category->posts_count }})</span>
        </a>
        @endforeach
    </div>
</section>

{{-- Latest Posts --}}
<section class="container-page pb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold">{{ __('Latest Articles') }}</h2>
        <a href="{{ route('blog.index') }}" class="btn btn-ghost text-sm">{{ __('View all') }} &rarr;</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($latest as $post)
        <x-web.partials.post-card :post="$post" />
        @endforeach
    </div>
</section>

{{-- Popular Posts --}}
<section class="bg-surface-muted/50 border-y border-border-subtle">
    <div class="container-page py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">{{ __('Popular') }}</h2>
            <a href="{{ route('blog.trending') }}" class="btn btn-ghost text-sm">{{ __('View all') }} &rarr;</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($popular as $post)
            <x-web.partials.post-card :post="$post" />
            @endforeach
        </div>
    </div>
</section>

{{-- Newsletter --}}
@include('web.partials.newsletter')
@endsection
