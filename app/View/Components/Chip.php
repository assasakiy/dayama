<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Chip extends Component
{
    public function __construct(
        public string $color = 'default',
        public ?string $href = null,
    ) {}

    public function render()
    {
        return function (array $data) {
            $tag = $this->href ? 'a' : 'span';
            $attrs = $this->href ? 'href="'.$this->href.'"' : '';

            return <<<HTML
                <{$tag} {$attrs} class="chip {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
                    {{ $slot }}
                </{$tag}>
            HTML;
        };
    }
}
