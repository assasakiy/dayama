<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SocialLinks extends Component
{
    public array $links = [
        'x' => 'https://x.com/modernblog',
        'facebook' => 'https://facebook.com/modernblog',
        'linkedin' => 'https://linkedin.com/company/modernblog',
        'rss' => '/feed',
    ];

    public function render()
    {
        return <<<'HTML'
            <div class="flex items-center gap-3" {{ $attributes }}>
                <a href="{{ $links['x'] }}" class="text-muted-foreground hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4l11.733 16h4.267l-11.733 -16z"/><path d="M4 20l6.768 -6.768m2.46 -2.46L20 4"/></svg>
                </a>
                <a href="{{ $links['facebook'] }}" class="text-muted-foreground hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="{{ $links['linkedin'] }}" class="text-muted-foreground hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                <a href="{{ $links['rss'] }}" class="text-muted-foreground hover:text-foreground transition-colors" target="_blank" rel="noopener noreferrer" aria-label="RSS Feed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/></svg>
                </a>
            </div>
        HTML;
    }
}
