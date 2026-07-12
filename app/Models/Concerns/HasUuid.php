<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Provides UUID primary key behavior to an Eloquent model.
 * Replaces auto-increment integer IDs with UUID v4 strings.
 */
trait HasUuid
{
    /**
     * The "booting" method of the trait.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (self $model): void {
            if ($model->getKey() === null) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }
        });
    }

    /**
     * Get the type of the primary key.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Get whether the primary key is incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }
}
