<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class NormalizeChecker implements ModerationChecker
{
    public function handle(ModerationContext $context, Closure $next): mixed
    {
        // Remove excessive newlines, trim, etc.
        $normalized = preg_replace("/[\r\n]+/", "\n", $context->normalizedContent);
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);
        $context->normalizedContent = trim($normalized);

        return $next($context);
    }
}
