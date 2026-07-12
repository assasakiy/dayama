@extends('web.layouts.app')

@section('title', $post->title . ' - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', $post->excerpt)
@section('og_title', $post->title)
@section('og_description', $post->excerpt)
@section('og_image', $post->cover_url ?? asset('images/og-default.png'))
@section('og_type', 'article')
@section('canonical', route('article.show', $post->slug))

@section('content')
    <article class="container-page py-8 md:py-12">
        <div class="max-w-3xl mx-auto">
            @include('web.partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

            @if ($post->cover_url)
                <figure class="mb-6">
                    <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" class="w-full rounded-md border border-border-subtle" />
                    @if ($post->cover_caption)
                        <figcaption class="text-xs text-muted-foreground mt-2 text-center">{{ $post->cover_caption }}</figcaption>
                    @endif
                </figure>
            @endif

            <h1 class="text-3xl md:text-4xl font-bold text-balance tracking-tight mb-4">{{ $post->title }}</h1>

            <div class="flex flex-wrap items-center gap-3 pb-6 border-b border-border-subtle mb-6">
                <x-avatar :user="$post->author" size="md" />
                <div>
                    <span class="text-sm font-medium block">{{ $post->author?->name ?? 'Unknown' }}</span>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <x-date :date="$post->published_at ?? $post->created_at" />
                        <span aria-hidden="true">&middot;</span>
                        <x-reading-time :minutes="$post->reading_time ?? 1" />
                    </div>
                </div>
            </div>

            @if ($post->excerpt)
                <p class="text-lg text-muted-foreground text-balance mb-6 leading-relaxed">{{ $post->excerpt }}</p>
            @endif

            @if (count($toc))
                <div x-data="{ open: false }" class="md:hidden mb-6">
                    <button x-on:click="open = !open" type="button" class="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                        Table of Contents
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{'rotate-180': open}" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <nav x-show="open" x-cloak class="mt-3 pl-4 border-l-2 border-border-subtle space-y-1.5">
                        @foreach ($toc as $heading)
                            <a href="#{{ $heading['id'] }}" class="block text-sm {{ $heading['level'] === 3 ? 'pl-3' : '' }} text-muted-foreground hover:text-foreground transition-colors">{{ $heading['text'] }}</a>
                        @endforeach
                    </nav>
                </div>
            @endif

            <div class="lg:flex lg:gap-10">
                @if (count($toc))
                    <aside class="hidden md:block shrink-0 w-56 order-1">
                        <nav class="sticky top-20 space-y-1.5 pl-0 border-l-2 border-border-subtle">
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground block mb-2 pl-3">On this page</span>
                            @foreach ($toc as $heading)
                                <a href="#{{ $heading['id'] }}" class="block text-sm {{ $heading['level'] === 3 ? 'pl-6' : 'pl-3' }} text-muted-foreground hover:text-foreground transition-colors py-0.5">{{ $heading['text'] }}</a>
                            @endforeach
                        </nav>
                    </aside>
                @endif

                <div class="flex-1 min-w-0 prose-blog max-w-none">
                    {!! $post->body !!}
                </div>
            </div>

            @if ($post->tags && $post->tags->count())
                <div class="flex flex-wrap items-center gap-2 mt-8 pt-6 border-t border-border-subtle">
                    <span class="text-xs text-muted-foreground font-medium">Tags:</span>
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" class="chip hover:bg-primary-muted hover:text-primary transition-colors">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center justify-between mt-6 pt-6 border-t border-border-subtle">
                @include('web.partials.share-buttons', ['url' => route('article.show', $post->slug), 'title' => $post->title])
            </div>

            <nav class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 pt-6 border-t border-border-subtle" aria-label="Article navigation">
                @if ($prevPost)
                    <a href="{{ route('article.show', $prevPost->slug) }}" class="group card-surface p-4 hover:border-primary-border transition-colors">
                        <span class="text-xs text-muted-foreground">Previous article</span>
                        <span class="block text-sm font-medium mt-1 group-hover:text-primary transition-colors">{{ $prevPost->title }}</span>
                    </a>
                @else
                    <div></div>
                @endif
                @if ($nextPost)
                    <a href="{{ route('article.show', $nextPost->slug) }}" class="group card-surface p-4 text-right hover:border-primary-border transition-colors sm:col-start-2">
                        <span class="text-xs text-muted-foreground">Next article</span>
                        <span class="block text-sm font-medium mt-1 group-hover:text-primary transition-colors">{{ $nextPost->title }}</span>
                    </a>
                @endif
            </nav>
        </div>
    </article>

    @if ($related->count())
        <section class="container-page pb-12" aria-labelledby="related-heading">
            <div class="max-w-3xl mx-auto">
                <h2 id="related-heading" class="text-lg font-semibold mb-6">Related articles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($related as $relatedPost)
                        <div>
                            @include('web.partials.post-card', ['post' => $relatedPost])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="container-page pb-16" aria-labelledby="comments-heading">
        <div class="max-w-3xl mx-auto">
            <h2 id="comments-heading" class="text-lg font-semibold mb-6">Comments</h2>
            <div class="card-surface p-5 mb-6">
                <h3 class="text-sm font-medium mb-3">Leave a comment</h3>
                @include('web.partials.comment-form', ['postId' => $post->id])
            </div>
            @include('web.partials.comment-thread', ['comments' => $comments])
        </div>
    </section>

    <section class="container-page pb-16">
        <div class="max-w-3xl mx-auto card-surface p-6 md:p-8 text-center">
            <h2 class="text-base font-semibold mb-2">Enjoying the read?</h2>
            <p class="text-sm text-muted-foreground mb-4">Subscribe to get new articles delivered to your inbox.</p>
            <div class="max-w-xs mx-auto">
                @include('web.partials.newsletter')
            </div>
        </div>
    </section>

    <script type="application/ld+json">{!! json_encode($jsonLd) !!}</script>
@endsection
