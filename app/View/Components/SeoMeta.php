<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SeoMeta extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?string $type = 'website',
    ) {}

    public function render()
    {
        return function () {
            $title = $this->title ?? 'Modern Blog';
            $description = $this->description ?? 'A modern blog about technology, design, and development.';
            $image = $this->image ?? asset('images/og-default.png');
            $url = $this->url ?? request()->url();

            return <<<HTML
                <meta property="og:title" content="{{ $title }}" />
                <meta property="og:description" content="{{ $description }}" />
                <meta property="og:image" content="{{ $image }}" />
                <meta property="og:type" content="{{ $type }}" />
                <meta property="og:url" content="{{ $url }}" />
                <meta property="og:site_name" content="Modern Blog" />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content="{{ $title }}" />
                <meta name="twitter:description" content="{{ $description }}" />
                <meta name="twitter:image" content="{{ $image }}" />
            HTML;
        };
    }
}
