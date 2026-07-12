@extends('web.layouts.app')

@section('title', $user->name . ' — ' . config('app.name'))
@section('description', $user->biography ?? __('Articles by :name', ['name' => $user->name]))
@section('og_image', $user->avatar_url)

@section('content')
<section class="container-page py-12">
    <div class="mb-8 flex items-start gap-4">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full bg-surface-muted shrink-0 object-cover aspect-square">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight flex items-center gap-2">
                {{ $user->name }}
                @if($user->is_verified)
                <svg class="w-6 h-6 text-primary fill-primary/10 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                @endif
            </h1>
            @if($user->biography)
            <p class="text-muted-foreground mt-1">{{ $user->biography }}</p>
            @endif

            @php
                $twitter = data_get($user->social_links, 'twitter');
                $github = data_get($user->social_links, 'github');
                $linkedin = data_get($user->social_links, 'linkedin');
            @endphp
            
            @if($user->website || $twitter || $github || $linkedin)
            <div class="flex items-center gap-3 mt-4">
                <span class="text-sm font-medium text-muted-foreground">{{ __('Follow on:') }}</span>
                @if($user->website)
                <a href="{{ str_starts_with($user->website, 'http') ? $user->website : 'https://'.$user->website }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary transition-colors" title="{{ __('Website') }}">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                </a>
                @endif
                
                @if($twitter)
                <a href="{{ str_starts_with($twitter, 'http') ? $twitter : 'https://twitter.com/'.ltrim(parse_url($twitter, PHP_URL_PATH) ?? $twitter, '/') }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary transition-colors" title="Twitter">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                </a>
                @endif

                @if($github)
                <a href="{{ str_starts_with($github, 'http') ? $github : 'https://github.com/'.ltrim(parse_url($github, PHP_URL_PATH) ?? $github, '/') }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary transition-colors" title="GitHub">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                </a>
                @endif

                @if($linkedin)
                <a href="{{ str_starts_with($linkedin, 'http') ? $linkedin : 'https://linkedin.com/in/'.ltrim(parse_url($linkedin, PHP_URL_PATH) ?? $linkedin, '/') }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary transition-colors" title="LinkedIn">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    @if($posts->isEmpty())
    <p class="text-muted-foreground py-16 text-center">{{ __('No articles by this author yet.') }}</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <x-web.partials.post-card :post="$post" />
        @endforeach
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</section>
@endsection
