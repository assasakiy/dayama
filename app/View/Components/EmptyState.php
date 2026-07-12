<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public function __construct(
        public string $title = 'Nothing here yet',
        public ?string $description = null,
        public ?string $action = null,
        public ?string $actionUrl = null,
    ) {}

    public function render()
    {
        return function () {
            return <<<HTML
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 mb-4 rounded-full bg-surface-muted flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground" aria-hidden="true"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-1">{{ $title }}</h3>
                    @if ($description)
                        <p class="text-muted-foreground text-sm max-w-sm mb-4">{{ $description }}</p>
                    @endif
                    @if ($action && $actionUrl)
                        <a href="{{ $actionUrl }}" class="btn btn-primary">{{ $action }}</a>
                    @endif
                </div>
            HTML;
        };
    }
}
