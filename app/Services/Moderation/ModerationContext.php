<?php

declare(strict_types=1);

namespace App\Services\Moderation;

use App\Enums\CommentStatus;
use Modules\CMS\Models\Post;
use Carbon\CarbonImmutable;

final class ModerationContext
{
    public string $normalizedContent;
    public int $score = 0;
    public array $flags = [];
    public ?CommentStatus $status = null;
    public readonly CarbonImmutable $submittedAt;

    public function __construct(
        public readonly string $rawContent,
        public readonly array $identity,
        public readonly ?Post $post = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $referer = null,
    ) {
        $this->normalizedContent = $rawContent;
        $this->submittedAt = now()->toImmutable();
    }

    public function addFlag(string $checkerName, int $scoreToAdd, array $details = []): void
    {
        $this->score += $scoreToAdd;
        $this->flags[$checkerName] = array_merge([
            'matched' => true,
            'score' => $scoreToAdd,
        ], $details);
    }
}
