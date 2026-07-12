@props(['comment'])
<div class="flex gap-3 py-4" id="comment-{{ $comment->id }}">
    <x-avatar :user="$comment->user" size="sm" class="shrink-0 mt-1" />
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-medium">{{ $comment->author_name ?? $comment->user?->name ?? 'Anonymous' }}</span>
            <span class="text-xs text-muted-foreground">
                <x-date :date="$comment->created_at" format="M j, Y" />
            </span>
        </div>
        <div class="mt-1 text-sm text-foreground prose-blog max-w-none [&_p]:my-1">
            {{ $comment->body }}
        </div>
        <div class="flex items-center gap-3 mt-2 text-xs text-muted-foreground">
            <button x-data x-on:click="$refs.replyForm{{ $comment->id }}?.classList.toggle('hidden'); $refs.replyInput{{ $comment->id }}?.focus()" type="button" class="hover:text-foreground transition-colors">Reply</button>
            <button x-data x-on:click="fetch('/comments/{{ $comment->id }}/react', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } }).then(r => r.json()).then(d => { if(d.success) location.reload(); })" type="button" class="hover:text-foreground transition-colors inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                <span>{{ $comment->reactions_count ?? 0 }}</span>
            </button>
        </div>
        <div x-ref="replyForm{{ $comment->id }}" class="hidden mt-3">
            <form x-data="{ body: '' }" x-on:submit.prevent="fetch('/comments', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ post_id: {{ $comment->post_id }}, parent_id: {{ $comment->id }}, body: body }) }).then(r => r.json()).then(d => { if(d.success) location.reload(); })" class="flex gap-2">
                <label for="reply-{{ $comment->id }}" class="sr-only">Write a reply</label>
                <input id="reply-{{ $comment->id }}" x-ref="replyInput{{ $comment->id }}" x-model="body" type="text" required placeholder="Write a reply..." class="flex-1 px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary placeholder:text-muted-foreground">
                <button type="submit" class="btn btn-primary text-sm" x-bind:disabled="!body.trim()">Reply</button>
            </form>
        </div>
        @if ($comment->replies && $comment->replies->count())
            <div class="ml-4 mt-2 border-l-2 border-border-subtle pl-4">
                @foreach ($comment->replies as $reply)
                    @include('web.partials.comment-item', ['comment' => $reply])
                @endforeach
            </div>
        @endif
    </div>
</div>
