<?php

namespace App\View\Components;

use App\Models\Post;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class PostMeta extends Component
{
    public function __construct(
        public Post $post,
        public bool $showAvatar = true,
        public bool $showComments = true,
    ) {}

    public function render()
    {
        return <<<'HTML'
            <div class="flex items-center gap-3 text-sm text-muted-foreground">
                @if ($showAvatar && $post->author)
                    <x-avatar :user="$post->author" size="sm" />
                @endif
                <span>{{ $post->author?->name ?? 'Unknown' }}</span>
                <span aria-hidden="true" class="text-border-strong">&middot;</span>
                <x-date :date="$post->published_at ?? $post->created_at" />
                <span aria-hidden="true" class="text-border-strong">&middot;</span>
                <x-reading-time :minutes="$post->reading_time ?? 1" />
                @if ($showComments && $post->comments_count !== null)
                    <span aria-hidden="true" class="text-border-strong">&middot;</span>
                    <span>{{ $post->comments_count }} {{ $post->comments_count === 1 ? 'comment' : 'comments' }}</span>
                @endif
            </div>
        HTML;
    }
}
