<?php

declare(strict_types=1);

namespace App\Services;

use Modules\CMS\Models\Post;
use App\Services\Moderation\Checkers\DuplicateChecker;
use App\Services\Moderation\Checkers\FinalDecisionChecker;
use App\Services\Moderation\Checkers\NormalizeChecker;
use App\Services\Moderation\Checkers\ProfanityChecker;
use App\Services\Moderation\Checkers\RateLimitChecker;
use App\Services\Moderation\Checkers\UrlChecker;
use App\Services\Moderation\ModerationContext;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;

class CommentModerationService
{
    /**
     * Run the moderation pipeline and return the evaluated context.
     */
    public function moderate(
        string $rawContent,
        array $identity,
        ?Post $post = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $referer = null
    ): ModerationContext {
        $context = new ModerationContext(
            rawContent: $rawContent,
            identity: $identity,
            post: $post,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            referer: $referer
        );

        return App::make(Pipeline::class)
            ->send($context)
            ->through([
                NormalizeChecker::class,
                RateLimitChecker::class,
                DuplicateChecker::class,
                UrlChecker::class,
                ProfanityChecker::class,
                // AI Checker would go here
                FinalDecisionChecker::class,
            ])
            ->thenReturn();
    }
}
