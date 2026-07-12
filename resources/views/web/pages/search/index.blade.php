@extends('web.layouts.app')

@section('title', 'Search - ' . ($siteSettings['site_name'] ?? 'Modern Blog'))
@section('description', 'Search articles, authors, and categories.')
@section('robots', 'noindex, follow')

@section('content')
    <div class="container-page py-12 md:py-16">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-balance mb-6">Search</h1>
            <form method="GET" action="{{ route('search') }}" role="search" class="mb-6">
                <div class="flex gap-2">
                    <label for="search-input" class="sr-only">Search articles</label>
                    <input id="search-input" type="search" name="q" value="{{ $q }}" placeholder="Search articles, authors, categories..." autocomplete="off" class="flex-1 px-4 py-2.5 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary placeholder:text-muted-foreground" />
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="flex items-center gap-3 mt-3">
                    <label class="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <input type="radio" name="type" value="all" {{ $type === 'all' ? 'checked' : '' }} onchange="this.form.submit()" class="accent-primary" /> All
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <input type="radio" name="type" value="articles" {{ $type === 'articles' ? 'checked' : '' }} onchange="this.form.submit()" class="accent-primary" /> Articles
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <input type="radio" name="type" value="authors" {{ $type === 'authors' ? 'checked' : '' }} onchange="this.form.submit()" class="accent-primary" /> Authors
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <input type="radio" name="type" value="categories" {{ $type === 'categories' ? 'checked' : '' }} onchange="this.form.submit()" class="accent-primary" /> Categories
                    </label>
                </div>
            </form>

            @if (strlen($q))
                <p class="text-sm text-muted-foreground mb-6">
                    {{ $total }} {{ $total === 1 ? 'result' : 'results' }} for "{{ $q }}"
                </p>
            @endif

            @if ($results->count())
                <div class="space-y-4">
                    @foreach ($results as $result)
                        @php $item = $result['data']; @endphp
                        @if ($result['type'] === 'article')
                            @include('web.partials.post-card-compact', ['post' => $item])
                        @elseif ($result['type'] === 'author')
                            <a href="{{ route('blog.author', $item->username) }}" class="flex items-center gap-3 py-3 border-b border-border-subtle last:border-0 group">
                                <x-avatar :user="$item" size="sm" />
                                <div>
                                    <span class="text-sm font-medium group-hover:text-primary transition-colors">{{ $item->name }}</span>
                                    <span class="text-xs text-muted-foreground block">Author</span>
                                </div>
                            </a>
                        @elseif ($result['type'] === 'category')
                            <a href="{{ route('blog.category', $item->slug) }}" class="flex items-center gap-3 py-3 border-b border-border-subtle last:border-0 group">
                                <div class="w-8 h-8 rounded-full bg-primary-muted flex items-center justify-center text-primary text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium group-hover:text-primary transition-colors">{{ $item->name }}</span>
                                    <span class="text-xs text-muted-foreground block">Category</span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @elseif (strlen($q))
                <x-empty-state title="No results found" description="Try different keywords or browse our articles." action="Browse articles" action-url="{{ route('blog.index') }}" />
            @endif
        </div>
    </div>
@endsection
