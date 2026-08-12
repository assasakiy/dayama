<?php

namespace App\View\Components;

use Modules\Core\Models\User;
use Illuminate\View\Component;

class Avatar extends Component
{
    public function __construct(
        public ?User $user = null,
        public string $size = 'md',
    ) {}

    public function render()
    {
        return function () {
            $sizes = ['sm' => 'w-8 h-8 text-xs', 'md' => 'w-10 h-10 text-sm', 'lg' => 'w-14 h-14 text-base'];
            $sizeClass = $sizes[$this->size] ?? $sizes['md'];
            $name = $this->user?->name ?? 'U';
            $initials = collect(explode(' ', $name))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
            $src = $this->user?->avatar_url ?? '';

            if ($src) {
                return <<<HTML
                    <img src="{$src}" alt="{{ $name }}" class="{$sizeClass} rounded-full object-cover border border-border-subtle" loading="lazy" />
                HTML;
            }

            return <<<HTML
                <span class="inline-flex items-center justify-center {$sizeClass} rounded-full bg-primary-muted text-primary font-semibold" aria-hidden="true">
                    {{ $initials }}
                </span>
            HTML;
        };
    }
}
