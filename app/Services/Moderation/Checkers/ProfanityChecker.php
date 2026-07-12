<?php

declare(strict_types=1);

namespace App\Services\Moderation\Checkers;

use App\Services\Moderation\ModerationChecker;
use App\Services\Moderation\ModerationContext;
use Closure;

class ProfanityChecker implements ModerationChecker
{
    private array $badWords = ['spam', 'viagra', 'casino', 'buy now']; // Example list

    public function handle(ModerationContext $context, Closure $next): mixed
    {
        $contentLower = strtolower($context->normalizedContent);
        $matches = [];

        foreach ($this->badWords as $word) {
            if (str_contains($contentLower, $word)) {
                $matches[] = $word;
            }
        }

        if (!empty($matches)) {
            $score = config('moderation.weights.profanity', 100);
            $context->addFlag('profanity', $score, [
                'matched' => true,
                'words' => $matches,
            ]);
        }

        return $next($context);
    }
}
