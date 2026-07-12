<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use App\Services\CommentRateLimiter;
use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class RateLimitChecker implements ModerationChecker
{
    public function __construct(
        private readonly CommentRateLimiter $limiter
    ) {}

    public function handle(ModerationContext $context, Closure $next): mixed
    {
        if ($this->limiter->tooManyAttempts($context->identity)) {
            $score = config('moderation.weights.rate_limit', 80);
            $context->addFlag('rate_limit', $score, ['matched' => true]);
        }

        return $next($context);
    }
}
