@extends('web.layouts.app')

@section('title', __('Authors') . ' — ' . config('app.name'))
@section('description', __('Meet our writers and contributors.'))
@section('og_title', __('Authors') . ' — ' . config('app.name'))

@section('content')
<section class="container-page py-12">
    <div class="mb-10">
        <a href="{{ route('blog.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">&larr; {{ __('Back to Post Archive') }}</a>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight mt-2">{{ __('Authors') }}</h1>
        <p class="text-muted-foreground mt-1">{{ __('Meet our writers and contributors.') }}</p>
    </div>

    @if($authors->isEmpty())
    <p class="text-muted-foreground py-16 text-center">{{ __('No authors yet.') }}</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($authors as $author)
        <div class="card p-6 hover:border-primary-border hover:shadow-elevated transition-all group relative">
            <div class="flex items-start gap-4">
                <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="w-12 h-12 rounded-full bg-surface-muted shrink-0 object-cover aspect-square" loading="lazy">
                <div class="min-w-0 flex-1">
                    <h2 class="font-semibold group-hover:text-primary transition-colors truncate flex items-center gap-1">
                        <a href="{{ route('author.show', $author) }}" class="focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            {{ $author->name }}
                        </a>
                        @if($author->is_verified)
                        <svg class="w-4 h-4 text-primary fill-primary/10 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                        @endif
                    </h2>
                    @if($author->biography)
                    <p class="text-sm text-muted-foreground mt-0.5 line-clamp-2">{{ $author->biography }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-2 text-xs text-muted-foreground">
                        <span>{{ $author->posts_count }} {{ __('articles') }}</span>
                    </div>
                    @php
                        $twitter = data_get($author->social_links, 'twitter');
                        $github = data_get($author->social_links, 'github');
                        $linkedin = data_get($author->social_links, 'linkedin');
                    @endphp
                    @if($author->website || $twitter || $github || $linkedin)
                    <div class="flex items-center gap-3 mt-3 text-xs text-muted-foreground relative z-10">
                        <span class="font-medium">{{ __('Follow on:') }}</span>
                        @if($author->website)
                        <a href="{{ str_starts_with($author->website, 'http') ? $author->website : 'https://'.$author->website }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="{{ __('Website') }}">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                        </a>
                        @endif
                        
                        @if($twitter)
                        <a href="{{ str_starts_with($twitter, 'http') ? $twitter : 'https://twitter.com/'.ltrim(parse_url($twitter, PHP_URL_PATH) ?? $twitter, '/') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="Twitter">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                        </a>
                        @endif

                        @if($github)
                        <a href="{{ str_starts_with($github, 'http') ? $github : 'https://github.com/'.ltrim(parse_url($github, PHP_URL_PATH) ?? $github, '/') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="GitHub">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                        </a>
                        @endif

                        @if($linkedin)
                        <a href="{{ str_starts_with($linkedin, 'http') ? $linkedin : 'https://linkedin.com/in/'.ltrim(parse_url($linkedin, PHP_URL_PATH) ?? $linkedin, '/') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="LinkedIn">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>
@endsection