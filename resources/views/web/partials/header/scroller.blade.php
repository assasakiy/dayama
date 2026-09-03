<!-- Header 2 (Categories Scroller) -->
<div class="h-12 bg-background/85 backdrop-blur-xl border-b border-border-subtle relative z-10 flex items-center shadow-sm">
    <div 
        x-data="{ 
            canScrollLeft: false, 
            canScrollRight: false,
            updateScroll() {
                const el = this.$refs.scrollContainer;
                if (!el) return;
                this.canScrollLeft = el.scrollLeft > 0;
                this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 2);
            }
        }" 
        x-init="setTimeout(() => updateScroll(), 100); window.addEventListener('resize', () => updateScroll())"
        class="container-page relative flex items-center w-full h-full"
    >
        <!-- Left Arrow -->
        <button 
            type="button"
            x-cloak
            x-show="canScrollLeft" 
            x-transition:enter="transition opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$refs.scrollContainer.scrollBy({left: -250, behavior: 'smooth'})" 
            class="absolute left-0 top-0 bottom-0 z-20 w-16 bg-gradient-to-r from-background via-background/80 to-transparent flex items-center justify-start pointer-events-auto"
            aria-label="Scroll left"
        >
            <div class="w-7 h-7 rounded-full bg-surface border border-border-subtle shadow-sm flex items-center justify-center text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </div>
        </button>

        <!-- Scrollable Categories Container -->
        <nav 
            x-ref="scrollContainer" 
            @scroll="updateScroll" 
            class="flex-1 overflow-x-auto flex items-center gap-6 md:gap-8 h-full scroll-smooth [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
        >
            @php 
                $categories = \Modules\CMS\Models\Category::where('is_visible', true)->orderBy('name')->get(); 
                $blogDomain = config('platform.sites.blog.domain', 'blog.' . config('platform.root_domain', 'dayama.test'));
                $blogUrl = 'http://' . $blogDomain;
            @endphp
            @foreach($categories as $category)
                <a href="{{ $blogUrl . route('category.show', $category, false) }}" class="shrink-0 text-sm font-medium text-muted-foreground hover:text-primary transition-colors whitespace-nowrap {{ request()->is('category/'.$category->slug) ? 'text-primary' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>

        <!-- Right Arrow -->
        <button 
            type="button"
            x-cloak
            x-show="canScrollRight" 
            x-transition:enter="transition opacity duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition opacity duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$refs.scrollContainer.scrollBy({left: 250, behavior: 'smooth'})" 
            class="absolute right-0 top-0 bottom-0 z-20 w-16 bg-gradient-to-l from-background via-background/80 to-transparent flex items-center justify-end pointer-events-auto"
            aria-label="Scroll right"
        >
            <div class="w-7 h-7 rounded-full bg-surface border border-border-subtle shadow-sm flex items-center justify-center text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
        </button>
    </div>
</div>
