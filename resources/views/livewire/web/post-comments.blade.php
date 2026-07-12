<div>
    <section class="max-w-[720px] mx-auto mt-10" aria-labelledby="comments-title">
        <h2 id="comments-title" class="text-lg font-semibold mb-6 flex items-center gap-2">
            {{ __('Comments') }} 
            <span class="bg-primary/10 text-primary text-xs px-2.5 py-0.5 rounded-full font-bold">{{ $comments->count() }}</span>
        </h2>
        
        @php
            // Extract all mentions from all comments to avoid N+1 queries
            $allText = $comments->pluck('content')->join(' ') . ' ' . $comments->flatMap->children->pluck('content')->join(' ');
            preg_match_all('/(?<=^|\s)@([A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+)*)/', $allText, $matches);
            $validUsernames = [];
            if (!empty($matches[1])) {
                $validUsernames = \App\Models\User::whereIn('username', array_unique($matches[1]))->pluck('username')->toArray();
            }

            $renderContent = function($text) use ($validUsernames) {
                $textWithMentions = preg_replace_callback('/(?<=^|\s)@([A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+)*)/', function($m) use ($validUsernames) {
                    if (in_array($m[1], $validUsernames)) {
                        return '[@' . $m[1] . '](' . url('/author/' . $m[1]) . ')';
                    }
                    return $m[0];
                }, $text);
                
                return \Illuminate\Support\Str::markdown($textWithMentions, ['html_input' => 'escape']);
            };
        @endphp



        {{-- Comment Form --}}
        <div class="mb-10 bg-surface border border-border-subtle rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex gap-4">
                @auth
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full bg-surface-muted shrink-0 object-cover hidden sm:block">
                @else
                <div class="w-10 h-10 rounded-full bg-surface-muted shrink-0 hidden sm:flex items-center justify-center text-muted-foreground border border-border-subtle">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                </div>
                @endauth
                
                <form wire:submit="submitComment" class="flex-1 min-w-0">
                    @guest
                        @if($hasCommentedAsGuest)
                            <div class="bg-surface-muted border border-border-subtle rounded-lg p-6 text-center text-sm text-muted-foreground">
                                Anda sudah memposting komentar tamu pada artikel ini. <br>
                                Silakan <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Login</a> atau <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Daftar</a> untuk berkomentar lagi.
                            </div>
                        @else
                            <div class="flex gap-3 mb-3">
                                <input type="text" wire:model="guest_name" placeholder="Name" class="flex-1 bg-background border border-border-subtle rounded-md px-3 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-muted-foreground/50" required>
                                <input type="email" wire:model="guest_email" placeholder="Email (will not be published)" class="flex-1 bg-background border border-border-subtle rounded-md px-3 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-muted-foreground/50" required>
                            </div>
                        @endif
                    @endguest

                    @if(auth()->check() || !$hasCommentedAsGuest)
                    <div class="bg-background border border-border-subtle focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all rounded-lg overflow-hidden flex flex-col">
                        <textarea wire:model="content" rows="3" class="w-full bg-transparent border-0 px-4 py-3 text-sm outline-none resize-none placeholder:text-muted-foreground/50 focus:ring-0" placeholder="{{ __('Write a comment... Markdown supported') }}" required></textarea>
                        
                        <div class="bg-surface-muted/30 px-3 py-2.5 flex items-center justify-between border-t border-border-subtle">
                            <span class="text-xs text-muted-foreground hidden sm:inline-block">{{ __('Markdown is supported.') }}</span>
                            <button type="submit" wire:loading.attr="disabled" wire:target="submitComment" class="btn btn-primary h-8 px-4 text-xs font-semibold tracking-wide rounded-md w-full sm:w-auto inline-flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="submitComment">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                </span>
                                <span wire:loading wire:target="submitComment">
                                    <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                                {{ __('Post Comment') }}
                            </button>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Comment List --}}
        <div class="space-y-6">
            @forelse($comments as $comment)
            <div class="mb-6 p-4 bg-surface rounded-md border border-border-subtle">
                <div class="flex items-start gap-3">
                    <img src="{{ $comment->author?->avatar_url ?? 'https://www.gravatar.com/avatar/?d=mp&s=40' }}" alt="" class="w-8 h-8 rounded-full shrink-0">
                    <div class="flex-1 min-w-0" x-data="{ showReplies: false }">
                        <div class="flex items-center gap-2 text-sm flex-wrap">
                            <span class="font-medium">{{ $comment->author?->name ?? $comment->guest_name }}</span>
                            @if($comment->created_as_guest)
                            <span class="text-[10px] text-muted-foreground bg-surface-muted border border-border-subtle rounded px-1.5 py-0.5 font-normal">Guest</span>
                            @endif
                            <span class="text-muted-foreground text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="prose prose-sm dark:prose-invert mt-1 text-pretty max-w-none prose-p:my-1 prose-a:text-primary prose-a:no-underline hover:prose-a:underline">
                            {!! $renderContent($comment->content) !!}
                        </div>

                        <div class="mt-2 flex items-center gap-4">
                            <button wire:click="setReplyingTo('{{ $comment->id }}')" class="text-xs text-muted-foreground hover:text-foreground font-medium transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 10 20 15 15 20"/><path d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                {{ __('Reply') }}
                            </button>
                            {{-- Like button — Alpine handles reactive state, no wire:loading to avoid affecting submit button --}}
                            <span x-data="{ liked: {{ ($comment->is_liked_by_me ?? false) ? 'true' : 'false' }}, count: {{ $comment->likes_count ?? 0 }} }"
                                x-on:like-toggled.window="if ($event.detail.id === '{{ $comment->id }}') { liked = $event.detail.liked; count = $event.detail.count; }">
                                <button
                                    wire:click="toggleLike('{{ $comment->id }}')"
                                    :class="liked ? 'text-red-500 hover:text-red-400' : 'text-muted-foreground hover:text-red-500'"
                                    class="text-xs font-medium transition-colors flex items-center gap-1 group/like"
                                    title="{{ __('Like') }}"
                                >
                                    <svg class="w-3.5 h-3.5 transition-all group-hover/like:scale-110"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        :fill="liked ? 'currentColor' : 'none'"
                                    ><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    <span x-show="count > 0" x-text="count"></span>
                                </button>
                            </span>
                            @if($comment->replies_count > 0)
                            <button @click="showReplies = !showReplies" class="text-xs text-muted-foreground hover:text-foreground font-medium transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3 transition-transform" :class="showReplies ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                {{ trans_choice('{1} View 1 Reply|[2,*] View :count Replies', $comment->replies_count, ['count' => $comment->replies_count]) }}
                            </button>
                            @endif
                        </div>

                        @if($replyingTo === $comment->id)
                        <div class="mt-4 mb-2">
                            <form wire:submit="submitReply" class="flex items-start gap-3 w-full">
                                @auth
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-8 h-8 rounded-full bg-surface-muted shrink-0 object-cover hidden sm:block mt-0.5">
                                @else
                                <div class="w-8 h-8 rounded-full bg-surface-muted shrink-0 hidden sm:flex items-center justify-center text-muted-foreground border border-border-subtle mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                </div>
                                @endauth
                                
                                <div class="flex-1 min-w-0">
                                    @guest
                                        @if($hasCommentedAsGuest)
                                            <div class="bg-surface-muted border border-border-subtle rounded-md p-4 mb-2 text-center text-[11px] text-muted-foreground">
                                                Anda sudah memposting komentar tamu. Silakan <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Login</a> untuk membalas.
                                            </div>
                                        @else
                                            <div class="flex gap-2 mb-2">
                                                <div class="flex-1">
                                                    <input type="text" wire:model="guest_name" placeholder="Name" class="w-full bg-background border border-border-subtle rounded-md px-3 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-muted-foreground/50" required>
                                                    @error('guest_name') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="flex-1">
                                                    <input type="email" wire:model="guest_email" placeholder="Email (private)" class="w-full bg-background border border-border-subtle rounded-md px-3 py-1.5 text-xs focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-muted-foreground/50" required>
                                                    @error('guest_email') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        @endif
                                    @endguest

                                    @if(auth()->check() || !$hasCommentedAsGuest)
                                    <div class="bg-background border border-border-subtle focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-all rounded-md overflow-hidden flex flex-col">
                                        <textarea wire:model="reply_content" rows="2" class="w-full bg-transparent border-0 px-3 py-2 text-sm outline-none resize-none placeholder:text-muted-foreground/50 focus:ring-0" placeholder="{{ __('Write a reply...') }}" required autofocus></textarea>
                                        @error('reply_content') <span class="text-xs text-red-500 px-3 py-1">{{ $message }}</span> @enderror
                                        @error('replyingTo') <span class="text-xs text-red-500 px-3 py-1">{{ $message }}</span> @enderror
                                        
                                        <div class="bg-surface-muted/30 px-2 py-2 flex items-center justify-end gap-2 border-t border-border-subtle">
                                            <button type="button" wire:click="cancelReply" class="btn h-7 px-3 text-[11px] font-semibold tracking-wide rounded border border-border-subtle bg-surface hover:bg-surface-muted text-muted-foreground">{{ __('Cancel') }}</button>
                                            <button type="submit" wire:loading.attr="disabled" wire:target="submitReply" class="btn btn-primary h-7 px-3 text-[11px] font-semibold tracking-wide rounded inline-flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span wire:loading.remove wire:target="submitReply">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                                </span>
                                                <span wire:loading wire:target="submitReply">
                                                    <svg class="animate-spin w-3 h-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </span>
                                                {{ __('Reply') }}
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                        @endif

                        {{-- Replies --}}
                        <div x-show="showReplies" x-cloak class="mt-4 ml-3 pl-4 border-l-2 border-border-subtle space-y-4">
                            @foreach($comment->children as $reply)
                            <div class="flex items-start gap-3">
                                <img src="{{ $reply->author?->avatar_url ?? 'https://www.gravatar.com/avatar/?d=mp&s=40' }}" alt="" class="w-6 h-6 rounded-full shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 text-sm flex-wrap">
                                        <span class="font-medium">{{ $reply->author?->name ?? $reply->guest_name }}</span>
                                        @if($reply->created_as_guest)
                                        <span class="text-[10px] text-muted-foreground bg-surface-muted border border-border-subtle rounded px-1.5 py-0.5 font-normal">Guest</span>
                                        @endif
                                        <span class="text-muted-foreground text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="prose prose-sm dark:prose-invert mt-1 text-pretty max-w-none prose-p:my-1 prose-a:text-primary prose-a:no-underline hover:prose-a:underline">
                                        {!! $renderContent($reply->content) !!}
                                    </div>
                                    <div class="mt-2 flex items-center gap-4">
                                        @php
                                            $mention = $reply->author && $reply->author->username ? '@' . $reply->author->username . ' ' : '';
                                        @endphp
                                        <button wire:click="$set('reply_content', '{{ $mention }}'); setReplyingTo('{{ $comment->id }}')" class="text-xs text-muted-foreground hover:text-foreground font-medium transition-colors flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 10 20 15 15 20"/><path d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                            {{ __('Reply') }}
                                        </button>
                                        {{-- Like button for reply — Alpine reactive state --}}
                                        <span x-data="{ liked: {{ ($reply->is_liked_by_me ?? false) ? 'true' : 'false' }}, count: {{ $reply->likes_count ?? 0 }} }"
                                            x-on:like-toggled.window="if ($event.detail.id === '{{ $reply->id }}') { liked = $event.detail.liked; count = $event.detail.count; }">
                                            <button
                                                wire:click="toggleLike('{{ $reply->id }}')"
                                                :class="liked ? 'text-red-500 hover:text-red-400' : 'text-muted-foreground hover:text-red-500'"
                                                class="text-xs font-medium transition-colors flex items-center gap-1 group/like"
                                                title="{{ __('Like') }}"
                                            >
                                                <svg class="w-3 h-3 transition-all group-hover/like:scale-110"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    :fill="liked ? 'currentColor' : 'none'"
                                                ><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                                <span x-show="count > 0" x-text="count"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted-foreground text-sm">{{ __('No comments yet. Be the first to share your thoughts!') }}</p>
            @endforelse
            
            @if($comments->count() < $totalComments || $perPage > 5)
            <div class="mt-8 flex justify-center gap-3">
                @if($perPage > 5)
                <button wire:click="loadLess" wire:loading.attr="disabled" wire:target="loadLess" class="btn btn-outline border-border-subtle hover:border-primary text-muted-foreground hover:text-foreground h-9 px-6 text-sm font-medium rounded-full inline-flex items-center gap-2.5 transition-all disabled:opacity-50">
                    <span wire:loading.remove wire:target="loadLess">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                    </span>
                    <span wire:loading wire:target="loadLess">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    <span>{{ __('Show Less') }}</span>
                </button>
                @endif
                
                @if($comments->count() < $totalComments)
                <button wire:click="loadMore" wire:loading.attr="disabled" wire:target="loadMore" class="btn btn-outline border-border-subtle hover:border-primary text-muted-foreground hover:text-foreground h-9 px-6 text-sm font-medium rounded-full inline-flex items-center gap-2.5 transition-all disabled:opacity-50">
                    <span wire:loading.remove wire:target="loadMore">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                    </span>
                    <span wire:loading wire:target="loadMore">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </span>
                    <span>{{ __('Load More Comments') }}</span>
                </button>
                @endif
            </div>
            @endif
        </div>
    </section>
</div>
