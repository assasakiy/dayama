<?php

declare(strict_types=1);

namespace App\Services\Moderation;

use Closure;

interface ModerationChecker
{
    /**
     * Handle the moderation check.
     */
    public function handle(ModerationContext $context, Closure $next): mixed;
}
