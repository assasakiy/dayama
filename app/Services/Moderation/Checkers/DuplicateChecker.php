<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use Modules\CMS\Models\Comment;
use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class DuplicateChecker implements ModerationChecker
{
    public function handle(ModerationContext $context, Closure $next): mixed
    {
        $recentContents = Comment::where('identity_key', $context->identity['key'])
            ->where('created_at', '>=', $context->submittedAt->subMinutes(10))
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('content');

        foreach ($recentContents as $content) {
            // Simplified exact match on normalized content. In a real app we might use similar_text or levenshtein.
            if ($content === $context->rawContent || $content === $context->normalizedContent) {
                $score = config('moderation.weights.duplicate', 80);
                $context->addFlag('duplicate', $score, ['matched' => true]);
                break;
            }
        }

        return $next($context);
    }
}
