@props(['post', 'currentCategory' => null])

<article class="group flex flex-col h-full gap-3 {{ $attributes->get('class') }}">
    {{-- Thumbnail --}}
    <a href="{{ route('blog.show', $post) }}" class="aspect-[16/9] overflow-hidden rounded-md border border-border-subtle bg-surface-muted block" tabindex="-1" aria-hidden="true">
        @if($post->thumbnail_url)
        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
        @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 via-primary-muted to-background">
            <div class="grid grid-cols-2 gap-2 opacity-30">
                <div class="w-6 h-6 rounded bg-primary/10"></div>
                <div class="w-6 h-6 rounded bg-primary/10 mt-2"></div>
                <div class="w-6 h-6 rounded bg-primary/10"></div>
                <div class="w-6 h-6 rounded bg-primary/10 mt-2"></div>
            </div>
        </div>
        @endif
    </a>

    {{-- Category --}}
    @php
        $displayCategory = $currentCategory ?? $post->primaryCategory ?? $post->categories->first();
    @endphp
    @if($displayCategory)
    <div class="flex items-center gap-2">
        <a href="{{ route('category.show', $displayCategory) }}" class="chip text-xs no-underline hover:bg-surface-muted transition-colors" style="--chip-color: {{ $displayCategory->color ?? 'var(--primary)' }}; color: {{ $displayCategory->color ?? 'var(--foreground)' }}; background-color: {{ $displayCategory->color ? $displayCategory->color.'1a' : 'var(--surface-muted)' }}; border-color: transparent;">
            {{ $displayCategory->name }}
        </a>
        <span class="text-xs text-muted-foreground">{{ $post->published_at?->diffForHumans() }}</span>
    </div>
    @endif

    {{-- Title --}}
    <h3 class="text-lg font-semibold leading-snug text-balance">
        <a href="{{ route('blog.show', $post) }}" class="hover:text-primary transition-colors no-underline">
            {{ $post->title }}
        </a>
    </h3>

    {{-- Excerpt --}}
    @php
        $displayExcerpt = $post->excerpt ?: Illuminate\Support\Str::limit(strip_tags($post->content), 120);
    @endphp
    @if($displayExcerpt)
    <p class="text-sm text-muted-foreground line-clamp-2 text-pretty">{{ $displayExcerpt }}</p>
    @endif

    {{-- Meta --}}
    <div class="flex items-center gap-3 text-xs text-muted-foreground mt-auto pt-2 border-t border-border-subtle">
        @if($post->author)
        <a href="{{ route('author.show', $post->author) }}" class="flex items-center gap-1.5 hover:text-foreground transition-colors no-underline">
            <img src="{{ $post->author->avatar_url }}" alt="" class="w-5 h-5 rounded-full bg-surface-muted object-cover aspect-square" loading="lazy">
            <span class="flex items-center gap-1">
                {{ $post->author->name }}
                @if($post->author->is_verified)
                <svg class="w-3.5 h-3.5 text-primary fill-primary/10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                @endif
            </span>
        </a>
        @endif
        <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $post->reading_time }} {{ __('min read') }}
        </span>
    </div>
</article>
