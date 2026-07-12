@extends('web.layouts.app')

@section('title', 'Articles - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', 'Browse all articles, tutorials, and guides.')
@section('og_type', 'website')

@section('content')
    <div class="container-page py-12 md:py-16">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-balance">Articles</h1>
                <p class="text-muted-foreground text-sm mt-1">{{ $totalPosts }} {{ $totalPosts === 1 ? 'article' : 'articles' }} published</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-8 pb-4 border-b border-border-subtle">
            <form method="GET" action="{{ route('blog.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select name="category" onchange="this.form.submit()" class="text-sm bg-surface border border-border-subtle rounded-sm px-3 py-1.5 focus:outline-none focus:border-primary">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ ($filters['category'] ?? '') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->posts_count }})</option>
                    @endforeach
                </select>
                <select name="tag" onchange="this.form.submit()" class="text-sm bg-surface border border-border-subtle rounded-sm px-3 py-1.5 focus:outline-none focus:border-primary">
                    <option value="">All Tags</option>
                    @foreach ($tags as $t)
                        <option value="{{ $t->slug }}" {{ ($filters['tag'] ?? '') === $t->slug ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
                <select name="sort" onchange="this.form.submit()" class="text-sm bg-surface border border-border-subtle rounded-sm px-3 py-1.5 focus:outline-none focus:border-primary">
                    <option value="recent" {{ ($filters['sort'] ?? '') === 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
                @foreach ($filters as $key => $value)
                    @if (!in_array($key, ['category', 'tag', 'sort']))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                @if (count(array_filter($filters ?? [])))
                    <a href="{{ route('blog.index') }}" class="btn btn-ghost text-xs">Clear filters</a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if ($posts->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($posts as $post)
                            @include('web.partials.post-card', ['post' => $post])
                        @endforeach
                    </div>
                    <div class="mt-8">
                        <x-pagination :paginator="$posts" />
                    </div>
                @else
                    <x-empty-state title="No articles found" description="Try adjusting your filters." action="Browse all" action-url="{{ route('blog.index') }}" />
                @endif
            </div>

            <aside class="space-y-8">
                <div class="card-surface p-5">
                    <h3 class="text-sm font-semibold mb-4">Popular</h3>
                    @forelse ($popular as $post)
                        @include('web.partials.post-card-compact', ['post' => $post])
                    @empty
                        <p class="text-sm text-muted-foreground">No popular articles yet.</p>
                    @endforelse
                </div>

                <div class="card-surface p-5">
                    <h3 class="text-sm font-semibold mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="chip hover:bg-primary-muted hover:text-primary hover:border-primary-border transition-colors">{{ $tag->name }}</a>
                        @empty
                            <span class="text-sm text-muted-foreground">No tags yet.</span>
                        @endforelse
                    </div>
                </div>

                <div class="card-surface p-5">
                    <h3 class="text-sm font-semibold mb-3">Categories</h3>
                    <ul class="space-y-1.5">
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('blog.category', $cat->slug) }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors flex items-center justify-between py-1">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-xs">{{ $cat->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-surface p-5">
                    <h3 class="text-sm font-semibold mb-3">Newsletter</h3>
                    <p class="text-xs text-muted-foreground mb-3">Get the latest articles delivered weekly.</p>
                    @include('web.partials.newsletter')
                </div>
            </aside>
        </div>
    </div>
@endsection
