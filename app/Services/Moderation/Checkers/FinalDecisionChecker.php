<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use App\Enums\CommentStatus;
use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class FinalDecisionChecker implements ModerationChecker
{
    public function handle(ModerationContext $context, Closure $next): mixed
    {
        $reviewThreshold = config('moderation.thresholds.review', 30);
        $spamThreshold = config('moderation.thresholds.spam', 60);

        if ($context->score >= $spamThreshold) {
            $context->status = CommentStatus::Spam;
        } elseif ($context->score >= $reviewThreshold) {
            $context->status = CommentStatus::Review;
        } else {
            $context->status = CommentStatus::Published;
        }

        return $next($context);
    }
}
