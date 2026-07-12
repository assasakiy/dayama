<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Reduces boilerplate when exposing slugged attributes.
 * Implementing model is expected to define ` sluggable(): array`.
 * Kept intentionally lightweight; core slug logic lives in services.
 */
trait HasSlug
{
    /**
     * Resolve the slug source value.
     */
    public function resolveSlugSource(): ?string
    {
        if (! method_exists($this, 'sluggable')) {
            return null;
        }
        $map = $this->sluggable();

        return collect($map)->map(fn ($source) => data_get($this, $source))->filter()->first();
    }
}
