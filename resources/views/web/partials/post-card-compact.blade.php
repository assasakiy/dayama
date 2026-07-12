@props(['post'])
<article class="group flex gap-4 items-start py-4 border-b border-border-subtle last:border-0">
    @if ($post->cover_url)
        <a href="{{ route('article.show', $post->slug) }}" class="shrink-0 w-20 h-20 rounded-md overflow-hidden bg-surface-muted" tabindex="-1" aria-hidden="true">
            <img src="{{ $post->cover_url }}" alt="" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]" loading="lazy" />
        </a>
    @endif
    <div class="flex-1 min-w-0">
        <h4 class="text-sm font-semibold text-balance">
            <a href="{{ route('article.show', $post->slug) }}" class="hover:text-primary transition-colors">{{ $post->title }}</a>
        </h4>
        <div class="flex items-center gap-2 mt-1 text-xs text-muted-foreground">
            <x-date :date="$post->published_at ?? $post->created_at" />
            <span aria-hidden="true">&middot;</span>
            <x-reading-time :minutes="$post->reading_time ?? 1" />
        </div>
    </div>
</article>
