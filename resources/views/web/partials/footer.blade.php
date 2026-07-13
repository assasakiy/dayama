@php
    $context = $context ?? 'blog';
    $siteName = \App\Services\SettingService::get('general.site_name', config('app.name'), $context);
    $tagline = \App\Services\SettingService::get('general.tagline', 'Modern Web Design', $context);
    $footerDesc = \App\Services\SettingService::get('general.description', 'A modern blog exploring technology, design, and development. We write about the things that matter.', $context);
    $logoUrl = \App\Services\SettingService::get('general.logo_url', null, $context);
@endphp
<footer class="mt-auto border-t border-border-subtle bg-surface-muted/50">
    <div class="container-page py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-foreground tracking-tight hover:opacity-80 transition-opacity mb-4">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto">
                    @else
                        <span class="w-10 h-10 rounded-md bg-primary flex items-center justify-center text-primary-foreground text-base font-bold shadow-sm">{{ substr($siteName, 0, 1) }}</span>
                    @endif
                    <div class="flex flex-col">
                        <span class="font-bold leading-none text-xl">{{ $siteName }}</span>
                        <span class="text-xs text-muted-foreground uppercase tracking-widest mt-1">{{ $tagline }}</span>
                    </div>
                </a>
                <p class="text-muted-foreground text-sm leading-relaxed max-w-xs">
                    {{ $footerDesc }}
                </p>
                <div class="mt-6">
                    <span class="text-sm font-semibold text-foreground">{{ __('Follow on') }}</span>
                    <div class="flex items-center gap-4 mt-3 text-muted-foreground">
                        <a href="{{ url('rss.xml') }}" class="hover:text-primary transition-colors" title="{{ __('RSS Feed') }}">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                        </a>
                        <a href="#" class="hover:text-primary transition-colors" title="Twitter">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                        </a>
                        <a href="#" class="hover:text-primary transition-colors" title="GitHub">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                        </a>
                        <a href="#" class="hover:text-primary transition-colors" title="LinkedIn">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Explore --}}
            <div>
                <h4 class="text-sm font-semibold text-foreground mb-3">{{ __('Explore') }}</h4>
                <ul class="space-y-2 text-sm text-muted-foreground">
                    <li><a href="{{ route('home') }}" class="hover:text-foreground transition-colors">{{ __('Post Archive') }}</a></li>
                    <li><a href="{{ route('blog.trending') }}" class="hover:text-foreground transition-colors">{{ __('Trending') }}</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:text-foreground transition-colors">{{ __('Categories') }}</a></li>
                    <li><a href="{{ route('tags.index') }}" class="hover:text-foreground transition-colors">{{ __('Tags') }}</a></li>
                    <li><a href="{{ route('authors.index') }}" class="hover:text-foreground transition-colors">{{ __('Authors') }}</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="text-sm font-semibold text-foreground mb-3">{{ __('Company') }}</h4>
                <ul class="space-y-2 text-sm text-muted-foreground">
                    <li><a href="{{ url('/about') }}" class="hover:text-foreground transition-colors">{{ __('About') }}</a></li>
                    <li><a href="{{ url('/contact') }}" class="hover:text-foreground transition-colors">{{ __('Contact') }}</a></li>
                    <li><a href="{{ url('/privacy-policy') }}" class="hover:text-foreground transition-colors">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="{{ url('/terms-of-service') }}" class="hover:text-foreground transition-colors">{{ __('Terms of Service') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted-foreground">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</p>
            <div class="flex items-center gap-4">
                <a href="{{ url('/privacy-policy') }}" class="hover:text-foreground transition-colors">{{ __('Privacy') }}</a>
                <a href="{{ url('/terms-of-service') }}" class="hover:text-foreground transition-colors">{{ __('Terms') }}</a>
                <a href="{{ url('sitemap.xml') }}" class="hover:text-foreground transition-colors">{{ __('Sitemap') }}</a>
            </div>
        </div>
    </div>
</footer>
