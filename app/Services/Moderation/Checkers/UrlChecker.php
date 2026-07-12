<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class UrlChecker implements ModerationChecker
{
    public function handle(ModerationContext $context, Closure $next): mixed
    {
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/', $context->normalizedContent);

        if ($urlCount > 0) {
            $score = config('moderation.weights.url', 30) * $urlCount;
            $context->addFlag('url', $score, [
                'matched' => true,
                'count' => $urlCount,
            ]);
        }

        return $next($context);
    }
}
