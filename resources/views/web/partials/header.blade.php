@php 
    $context = $context ?? 'blog';
    $categories = \App\Models\Category::where('is_visible', true)->orderBy('name')->get(); 
    $siteName = \App\Services\SettingService::get('general.site_name', config('app.name'), $context);
    $tagline = \App\Services\SettingService::get('general.tagline', 'Modern Web Design', $context);
    $logoUrl = \App\Services\SettingService::get('general.logo_url', null, $context);
@endphp
<header 
    x-data="{ 
        scrolledDown: false, 
        lastScroll: 0,
        handleScroll() {
            let current = window.scrollY;
            
            // Add a threshold so it doesn't trigger on micro-scrolls
            if (Math.abs(current - this.lastScroll) < 20) {
                return;
            }
            
            if (current > 100 && current > this.lastScroll) {
                this.scrolledDown = true;
            } else if (current < this.lastScroll) {
                this.scrolledDown = false;
            }
            
            this.lastScroll = current;
        }
    }" 
    @scroll.window="handleScroll"
    :class="scrolledDown ? '-translate-y-16' : 'translate-y-0'"
    class="sticky top-0 z-50 flex flex-col transition-transform duration-300 ease-in-out"
>
    <!-- Header 1 -->
    <div class="h-16 bg-background border-b border-border-subtle relative z-20 flex items-center transition-colors">
        <div class="container-page flex items-center justify-between w-full h-full">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-foreground tracking-tight hover:opacity-80 transition-opacity">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-8 w-auto">
                @else
                    <span class="w-8 h-8 rounded bg-primary flex items-center justify-center text-primary-foreground text-sm font-bold shadow-sm">{{ substr($siteName, 0, 1) }}</span>
                @endif
                <div class="flex flex-col">
                    <span class="font-bold leading-none text-lg">{{ $siteName }}</span>
                    <span class="text-[10px] text-muted-foreground uppercase tracking-widest mt-0.5">{{ $tagline }}</span>
                </div>
            </a>

            {{-- Nav --}}
            <nav class="hidden lg:flex items-center gap-1" role="navigation" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('home') }}" class="btn btn-ghost text-sm">{{ __('Home') }}</a>
                <a href="#" class="btn btn-ghost text-sm">{{ __('Service') }}</a>
                <a href="{{ url('/about') }}" class="btn btn-ghost text-sm">{{ __('About') }}</a>
                <a href="{{ url('/contact') }}" class="btn btn-ghost text-sm">{{ __('Contact') }}</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5 sm:gap-3">
                <button type="button" class="btn btn-ghost p-2 rounded-full" aria-label="{{ __('Search') }}" onclick="document.getElementById('search-modal').showModal()">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="btn btn-ghost p-2 rounded-full" id="theme-toggle" aria-label="{{ __('Toggle dark mode') }}" x-data @click="$store.theme.toggle()">
                    <svg class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                @auth
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false" class="btn btn-ghost p-2 rounded-full relative" aria-label="{{ __('Notifications') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1.5 w-2 h-2 bg-primary rounded-full ring-2 ring-background"></span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-2 w-80 bg-surface border border-border-subtle rounded-xl shadow-lg z-50 overflow-hidden text-left">
                            <div class="px-4 py-3 border-b border-border-subtle flex justify-between items-center bg-surface-muted/30">
                                <h3 class="text-sm font-semibold">{{ __('Notifications') }}</h3>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.read.all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-primary hover:underline font-medium">{{ __('Mark all as read') }}</button>
                                </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-surface-muted transition-colors border-b border-border-subtle last:border-0">
                                    <p class="text-xs text-foreground">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                    <span class="text-[10px] text-muted-foreground mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                </a>
                                @empty
                                <div class="px-4 py-6 text-center text-muted-foreground text-sm">
                                    {{ __('No new notifications') }}
                                </div>
                                @endforelse
                            </div>
                            <div class="p-2 border-t border-border-subtle text-center bg-surface-muted/30">
                                <a href="{{ route('dashboard.index') }}" class="text-[11px] text-foreground font-medium hover:text-primary transition-colors">{{ __('View all in Dashboard') }}</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('dashboard.index') }}" class="hidden lg:inline-flex btn btn-primary text-sm h-9 px-4 rounded-full shadow-sm">{{ __('Dashboard') }}</a>
                @else
                    <div class="hidden lg:flex items-center gap-2 border-l border-border-subtle pl-4 ml-1">
                        <a href="{{ route('login') }}" class="btn btn-ghost text-sm h-9 px-4 rounded-full">{{ __('Sign In') }}</a>
                        <a href="{{ url('/register') }}" class="btn btn-primary text-sm h-9 px-4 rounded-full shadow-sm">{{ __('Sign Up') }}</a>
                    </div>
                @endauth

                {{-- Mobile menu toggle --}}
                <button type="button" class="lg:hidden btn btn-ghost p-2 rounded-full" aria-label="{{ __('Toggle menu') }}" x-data @click="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Header 2 -->
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
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category) }}" class="shrink-0 text-sm font-medium text-muted-foreground hover:text-primary transition-colors whitespace-nowrap {{ request()->is('category/'.$category->slug) ? 'text-primary' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </nav>

            <!-- Right Arrow -->
            <button 
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

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden lg:hidden border-t border-border-subtle px-4 py-3 bg-background shadow-md">
        <nav class="flex flex-col gap-1">
            <a href="{{ route('home') }}" class="btn btn-ghost justify-start">{{ __('Home') }}</a>
            <a href="#" class="btn btn-ghost justify-start">{{ __('Service') }}</a>
            <a href="{{ url('/about') }}" class="btn btn-ghost justify-start">{{ __('About') }}</a>
            <a href="{{ url('/contact') }}" class="btn btn-ghost justify-start">{{ __('Contact') }}</a>
            
            <div class="h-px bg-border-subtle my-2"></div>
            
            @auth
                <a href="{{ route('dashboard.index') }}" class="btn btn-primary justify-center mt-2">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost justify-start">{{ __('Sign In') }}</a>
                <a href="{{ url('/register') }}" class="btn btn-primary justify-center mt-1">{{ __('Sign Up') }}</a>
            @endauth
        </nav>
    </div>
</header>

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
