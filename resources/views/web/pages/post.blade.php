@extends('web.layouts.app')

@php
    $seoTitle = $post->seo_title ?: $post->title;
    $seoDesc = $post->seo_description 
        ?: $post->excerpt 
        ?: Illuminate\Support\Str::limit(strip_tags($post->content), 160);
    $canonicalUrl = $post->canonical_url ?: url()->current();
    
    $rawOgImage = $post->getFirstMediaUrl('cover', 'large') 
        ?: $post->getFirstMediaUrl('cover') 
        ?: $post->getFirstMediaUrl('thumbnail', 'large') 
        ?: $post->getFirstMediaUrl('thumbnail') 
        ?: $post->thumbnail_url 
        ?: asset('img/og-default.png');
    $ogImage = parse_url($rawOgImage, PHP_URL_PATH);
@endphp

@section('title', $seoTitle . ' — ' . config('app.name'))
@section('description', $seoDesc)
@section('canonical', $canonicalUrl)
@section('og_title', $seoTitle)
@section('og_description', $seoDesc)
@section('og_type', 'article')
@section('og_url', $canonicalUrl)
@section('og_image', $ogImage)
@section('twitter_card', $post->og_data['twitter_card_type'] ?? 'summary_large_image')
@section('twitter_title', $seoTitle)
@section('twitter_description', $seoDesc)
@section('twitter_image', $ogImage)

@if($post->meta_keywords && count($post->meta_keywords))
@section('keywords', implode(', ', $post->meta_keywords))
@elseif($post->tags && $post->tags->count())
@section('keywords', $post->tags->pluck('name')->implode(', '))
@endif

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ str_replace('"', '\"', $seoTitle) }}",
    "description": "{{ str_replace('"', '\"', $seoDesc) }}",
    "image": "{{ $ogImage }}",
    "datePublished": "{{ ($post->published_at ?? $post->created_at)->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Person",
        "name": "{{ $post->author->name }}",
        "url": "{{ route('author.show', $post->author) }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name') }}"
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $canonicalUrl }}"
    }
}
</script>
@endpush

@section('content')
<article class="container-page py-8 lg:py-12">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6" aria-label="{{ __('Breadcrumb') }}">
        <a href="{{ route('home') }}" class="hover:text-foreground transition-colors">{{ __('Home') }}</a>
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('blog.index') }}" class="hover:text-foreground transition-colors">{{ __('Post') }}</a>
        @if($post->primaryCategory)
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('category.show', $post->primaryCategory) }}" class="hover:text-foreground transition-colors">{{ $post->primaryCategory->name }}</a>
        @endif
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-foreground truncate max-w-[200px]">{{ $post->title }}</span>
    </nav>

    {{-- Header --}}
    <header class="max-w-[960px] mx-auto mb-8 text-center flex flex-col items-center">
        @if($post->categories && $post->categories->count() > 0)
        <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
            @foreach($post->categories as $cat)
            <a href="{{ route('category.show', $cat) }}" class="chip text-xs">{{ $cat->name }}</a>
            @endforeach
        </div>
        @endif
        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold tracking-tight text-balance leading-[1.15]">
            {{ $post->title }}
        </h1>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-4 text-sm text-muted-foreground">
            <a href="{{ route('author.show', $post->author) }}" class="flex items-center gap-2 hover:text-foreground transition-colors no-underline">
                <img src="{{ $post->author->avatar_url }}" alt="" class="w-8 h-8 rounded-full bg-surface-muted object-cover aspect-square" loading="lazy">
                <span class="font-medium flex items-center gap-1">
                    {{ $post->author->name }}
                    @if($post->author->is_verified)
                    <svg class="w-4 h-4 text-primary fill-primary/10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    @endif
                </span>
            </a>
            <span class="text-border-strong">&middot;</span>
            <time datetime="{{ ($post->published_at ?? $post->created_at)->toIso8601String() }}">{{ ($post->published_at ?? $post->created_at)->format('F j, Y') }}</time>
            <span class="text-border-strong">&middot;</span>
            <span>{{ $post->reading_time }} {{ __('min read') }}</span>
        </div>
    </header>

    {{-- Cover Image --}}
    @php
        $rawCoverImage = $post->getFirstMediaUrl('cover', 'large') 
            ?: $post->getFirstMediaUrl('cover') 
            ?: $post->getFirstMediaUrl('thumbnail', 'large') 
            ?: $post->getFirstMediaUrl('thumbnail') 
            ?: $post->thumbnail_url;
        $coverImage = $rawCoverImage ? parse_url($rawCoverImage, PHP_URL_PATH) : null;
    @endphp
    @if($coverImage)
    <div class="mb-8 rounded-md overflow-hidden border border-border-subtle">
        <img src="{{ $coverImage }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[500px] object-cover" loading="eager">
    </div>
    @endif

    {{-- Content + Sidebar --}}
    <div class="flex gap-10 max-w-[960px] mx-auto">
        {{-- Table of Contents (sidebar) --}}
        <aside 
            x-data="{ 
                scrolledDown: false, 
                lastScroll: 0,
                handleScroll() {
                    let current = window.scrollY;
                    if (Math.abs(current - this.lastScroll) < 20) return;
                    if (current > 100 && current > this.lastScroll) {
                        this.scrolledDown = true;
                    } else if (current < this.lastScroll) {
                        this.scrolledDown = false;
                    }
                    this.lastScroll = current;
                }
            }"
            @scroll.window="handleScroll"
            class="hidden lg:block w-56 shrink-0"
        >
            <div class="sticky transition-all duration-300 ease-in-out" :class="scrolledDown ? 'top-20' : 'top-32'">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3">{{ __('On this page') }}</h4>
                <nav id="toc" class="text-sm space-y-1.5 text-muted-foreground" aria-label="{{ __('Table of contents') }}">
                    {{-- Generated by JS or manually --}}
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            <div class="prose-blog max-w-none">
                {!! $post->content !!}
            </div>

            <div class="mt-10 pt-6 border-t border-border-subtle flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                {{-- Tags --}}
                <div class="flex items-center gap-3">
                    @if($post->tags->isNotEmpty())
                    <span class="text-sm font-medium text-muted-foreground">{{ __('Tags') }}:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                        <a href="{{ route('tag.show', $tag) }}" class="chip text-xs bg-surface-muted hover:bg-border-subtle hover:text-foreground transition-colors no-underline">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Interactions & Share --}}
                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                    {{-- Like & Bookmark --}}
                    <div class="flex items-center gap-1" x-data="{
                        isLiked: {{ $isLiked ? 'true' : 'false' }},
                        isBookmarked: {{ $isBookmarked ? 'true' : 'false' }},
                        likes: {{ $post->reactions_count ?? 0 }},
                        loadingLike: false,
                        loadingBookmark: false,
                        showLoginModal: false,
                        toggleLike() {
                            if (this.loadingLike) return;
                            
                            // Optimistic update
                            this.isLiked = !this.isLiked;
                            this.likes += this.isLiked ? 1 : -1;
                            
                            this.loadingLike = true;
                            fetch('{{ route('blog.reaction', $post) }}', {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ type: 'like' })
                            })
                            .then(res => {
                                if (res.status === 401) {
                                    window.location.href = '{{ route('login') }}';
                                    throw new Error('Unauthorized');
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (data.message === 'Reaction removed.') {
                                    this.isLiked = false;
                                } else if (data.message === 'Reaction added.' || data.message === 'Reaction updated.') {
                                    this.isLiked = true;
                                }
                                
                                if (data.post && data.post.reactions_count !== undefined) {
                                    this.likes = data.post.reactions_count;
                                }
                            })
                            .catch(err => {
                                console.log(err);
                                // Revert on error
                                this.isLiked = !this.isLiked;
                                this.likes += this.isLiked ? 1 : -1;
                            })
                            .finally(() => this.loadingLike = false);
                        },
                        toggleBookmark() {
                            @guest
                            {{-- Guest: show login modal instead of making API call --}}
                            this.showLoginModal = true;
                            return;
                            @endguest
                            if (this.loadingBookmark) return;
                            this.loadingBookmark = true;
                            fetch('{{ route('blog.bookmark', $post) }}', {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => {
                                if (res.status === 401) {
                                    this.showLoginModal = true;
                                    throw new Error('Unauthorized');
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (data.bookmarked !== undefined) {
                                    this.isBookmarked = data.bookmarked;
                                }
                            })
                            .catch(err => console.log(err))
                            .finally(() => this.loadingBookmark = false);
                        }
                    }">
                        <button @click="toggleLike()" :class="isLiked ? 'text-red-500' : 'text-muted-foreground hover:text-foreground'" class="btn btn-ghost p-2 flex items-center gap-1.5 transition-colors rounded-full group/like" aria-label="{{ __('Like post') }}" :disabled="loadingLike">
                            <svg class="w-5 h-5 transition-all group-hover/like:scale-110"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                :fill="isLiked ? 'currentColor' : 'none'"
                            >
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                            <span class="text-sm font-medium" x-text="likes"></span>
                        </button>
                        
                        <button @click="toggleBookmark()" :class="isBookmarked ? 'text-primary' : 'text-muted-foreground hover:text-foreground'" class="btn btn-ghost p-2 flex items-center transition-colors rounded-full group/bm" aria-label="{{ __('Bookmark post') }}" :disabled="loadingBookmark">
                            <svg class="w-5 h-5 transition-all group-hover/bm:scale-110"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                :fill="isBookmarked ? 'currentColor' : 'none'"
                            >
                                <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>
                            </svg>
                        </button>

                        {{-- Login Modal for Guest --}}
                        <div x-show="showLoginModal" x-cloak x-transition.opacity class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" @click.self="showLoginModal = false">
                            <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-surface border border-border-subtle rounded-2xl shadow-xl p-6 max-w-sm w-full relative">
                                <button @click="showLoginModal = false" class="absolute top-3 right-3 p-1.5 rounded-full hover:bg-surface-muted text-muted-foreground transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                                    </div>
                                    <h3 class="text-lg font-semibold mb-1">{{ __('Save this article') }}</h3>
                                    <p class="text-muted-foreground text-sm mb-5">{{ __('Create a free account to bookmark articles and access your personal reading list anytime.') }}</p>
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('register') }}" class="btn btn-primary w-full h-10 flex items-center justify-center gap-2 font-semibold rounded-lg">
                                            {{ __('Create Free Account') }}
                                        </a>
                                        <a href="{{ route('login') }}" class="btn h-10 flex items-center justify-center gap-2 border border-border-subtle bg-surface hover:bg-surface-muted text-sm text-foreground rounded-lg">
                                            {{ __('Sign In') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Share --}}
                    <div class="flex items-center gap-1 text-sm text-muted-foreground">
                        <span class="mr-2">{{ __('Share') }}:</span>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost p-1.5 rounded-full" aria-label="{{ __('Share on Twitter') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost p-1.5 rounded-full" aria-label="{{ __('Share on Facebook') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost p-1.5 rounded-full" aria-label="{{ __('Share on LinkedIn') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Author Bio --}}
    <div class="max-w-[720px] mx-auto mt-10 p-6 bg-surface rounded-md border border-border-subtle">
        <div class="flex items-start gap-4">
            <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="w-12 h-12 rounded-full bg-surface-muted shrink-0 object-cover">
            <div>
                <a href="{{ route('author.show', $post->author) }}" class="font-semibold hover:text-primary transition-colors flex items-center gap-1">
                    {{ $post->author->name }}
                    @if($post->author->is_verified)
                    <svg class="w-4 h-4 text-primary fill-primary/10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    @endif
                </a>
        @if($post->author->biography)
                <p class="text-sm text-muted-foreground mt-1">{{ $post->author->biography }}</p>
                @endif
            </div>
        </div>
    </div>

    <livewire:web.post-comments :post="$post" />

    {{-- Previous / Next --}}
    <nav class="max-w-[720px] mx-auto mt-12 pt-8 border-t border-border-subtle" aria-label="{{ __('Previous and next articles') }}">
        @php
            $currentDate = $post->published_at ?? $post->created_at;
            $prev = $post->where('published_at', '<', $currentDate)->where('status', 'published')->orderBy('published_at', 'desc')->first();
            $next = $post->where('published_at', '>', $currentDate)->where('status', 'published')->orderBy('published_at', 'asc')->first();
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Previous Article Card --}}
            @if($prev)
            <a href="{{ route('blog.show', $prev) }}" class="group relative flex flex-col p-4 md:p-5 bg-surface border border-border-subtle rounded-xl hover:-translate-y-1 hover:shadow-pop hover:border-border-strong transition-all duration-300 no-underline text-left">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                    {{ __('Previous Article') }}
                </div>
                <div class="flex gap-4 items-center">
                    @if($prev->thumbnail_url)
                    <img src="{{ $prev->thumbnail_url }}" alt="" class="w-16 h-16 rounded-md object-cover shrink-0 hidden sm:block bg-surface-muted" loading="lazy">
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-bold text-foreground leading-snug line-clamp-2 mb-2 group-hover:text-primary transition-colors">{{ $prev->title }}</h4>
                        <div class="flex items-center gap-2 text-xs text-muted-foreground flex-wrap">
                            @if($prev->category)
                            <span class="chip text-[10px] py-0 px-2 border-none bg-surface-muted">{{ $prev->category->name }}</span>
                            @endif
                            <span>{{ $prev->reading_time }} {{ __('min read') }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @else
            <div class="hidden md:block"></div>
            @endif

            {{-- Next Article Card --}}
            @if($next)
            <a href="{{ route('blog.show', $next) }}" class="group relative flex flex-col p-4 md:p-5 bg-surface border border-border-subtle rounded-xl hover:-translate-y-1 hover:shadow-pop hover:border-border-strong transition-all duration-300 no-underline text-right">
                <div class="flex items-center justify-end gap-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3">
                    {{ __('Next Article') }}
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                <div class="flex gap-4 items-center justify-end">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-bold text-foreground leading-snug line-clamp-2 mb-2 group-hover:text-primary transition-colors">{{ $next->title }}</h4>
                        <div class="flex items-center justify-end gap-2 text-xs text-muted-foreground flex-wrap">
                            @if($next->category)
                            <span class="chip text-[10px] py-0 px-2 border-none bg-surface-muted">{{ $next->category->name }}</span>
                            @endif
                            <span>{{ $next->reading_time }} {{ __('min read') }}</span>
                        </div>
                    </div>
                    @if($next->thumbnail_url)
                    <img src="{{ $next->thumbnail_url }}" alt="" class="w-16 h-16 rounded-md object-cover shrink-0 hidden sm:block bg-surface-muted" loading="lazy">
                    @endif
                </div>
            </a>
            @else
            <div class="hidden md:block"></div>
            @endif
        </div>
    </nav>

    {{-- Related Posts --}}
    @if($related->isNotEmpty())
    <section class="container-page mt-12 pt-8 border-t border-border-subtle px-0" aria-labelledby="related-title">
        <h2 id="related-title" class="text-xl font-semibold mb-6">{{ __('Related Articles') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($related as $r)
            <x-web.partials.post-card :post="$r" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Newsletter --}}
    @include('web.partials.newsletter')
</article>
@endsection

@push('scripts')
<script>
    // Generate TOC from article headings
    (function() {
        const toc = document.getElementById('toc');
        const headings = document.querySelectorAll('.prose-blog h2, .prose-blog h3');
        if (!toc || headings.length === 0) return;

        headings.forEach((h, i) => {
            const id = `section-${i}`;
            h.id = id;
            h.style.scrollMarginTop = '5rem';
            const a = document.createElement('a');
            a.href = `#${id}`;
            a.className = `block ${h.tagName === 'H3' ? 'pl-3' : ''} hover:text-foreground transition-colors`;
            a.textContent = h.textContent;
            toc.appendChild(a);
        });
    })();
</script>
@endpush
