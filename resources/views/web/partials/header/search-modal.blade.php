{{-- Search Modal --}}
<dialog id="search-modal" class="backdrop:bg-black/60 backdrop:backdrop-blur-sm bg-transparent w-full max-w-2xl mx-auto rounded-2xl shadow-none p-4 outline-none fixed top-1/4" onclick="if(event.target === this) this.close()">
    <div class="bg-background border border-border-subtle rounded-2xl shadow-2xl overflow-hidden" onclick="event.stopPropagation()">
        <form action="{{ route('search') }}" method="GET" class="relative flex items-center p-2">
            <svg class="absolute left-6 w-5 h-5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" name="q" placeholder="{{ __('Search for articles, authors, or categories...') }}" class="w-full h-14 pl-14 pr-16 bg-transparent border-none outline-none text-base md:text-lg focus:ring-0" autofocus>
            <button type="button" class="absolute right-4 text-xs font-bold text-muted-foreground bg-surface-muted hover:bg-border-subtle transition-colors px-2 py-1.5 rounded-md border border-border-subtle" onclick="document.getElementById('search-modal').close()">ESC</button>
        </form>
    </div>
</dialog>
