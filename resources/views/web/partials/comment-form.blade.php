@props(['postId'])
@auth
    <form x-data="{ body: '', submitted: false }" x-on:submit.prevent="fetch('/comments', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ post_id: {{ $postId }}, body: body }) }).then(r => r.json()).then(d => { if(d.success) { submitted = true; body = ''; location.reload(); } })" class="space-y-3">
        <label for="comment-body" class="sr-only">Write a comment</label>
        <textarea id="comment-body" x-model="body" rows="4" required placeholder="Write your comment..." class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary placeholder:text-muted-foreground resize-y"></textarea>
        <button type="submit" class="btn btn-primary text-sm" x-bind:disabled="!body.trim()">Post Comment</button>
    </form>
@else
    <form x-data="{ name: '', email: '', website: '', body: '', agreed: false, submitted: false, errors: {} }" x-on:submit.prevent="fetch('/comments', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: JSON.stringify({ post_id: {{ $postId }}, name, email, website, body, gdpr: agreed }) }).then(r => r.json()).then(d => { if(d.success) { submitted = true; body = ''; location.reload(); } else { errors = d.errors || {}; } })" class="space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="comment-name" class="block text-sm font-medium mb-1">Name <span class="text-danger">*</span></label>
                <input id="comment-name" x-model="name" type="text" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="comment-email" class="block text-sm font-medium mb-1">Email <span class="text-danger">*</span></label>
                <input id="comment-email" x-model="email" type="email" required class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
            </div>
        </div>
        <div>
            <label for="comment-website" class="block text-sm font-medium mb-1">Website</label>
            <input id="comment-website" x-model="website" type="url" class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary">
        </div>
        <div>
            <label for="comment-body-guest" class="sr-only">Comment</label>
            <textarea id="comment-body-guest" x-model="body" rows="4" required placeholder="Write your comment..." class="w-full px-3 py-2 text-sm bg-surface border border-border-subtle rounded-sm focus:outline-none focus:border-primary placeholder:text-muted-foreground resize-y"></textarea>
        </div>
        <label class="flex items-start gap-2 text-sm text-muted-foreground">
            <input x-model="agreed" type="checkbox" required class="mt-1 accent-primary">
            <span>I consent to having my name, email, and website stored for the purpose of displaying this comment. <a href="{{ url('/privacy-policy') }}" class="link text-sm">Privacy Policy</a></span>
        </label>
        <button type="submit" class="btn btn-primary text-sm" x-bind:disabled="!body.trim() || !agreed">Post Comment</button>
    </form>
@endauth
