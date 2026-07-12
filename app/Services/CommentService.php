<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class CommentService
{
    private const MAX_DEPTH = 2;

    public function __construct(
        private readonly CommentModerationService $moderationService
    ) {}

    /**
     * Add a top-level comment to a post.
     */
    public function addComment(Post $post, array $identity, array $data, ?string $ipAddress = null, ?string $userAgent = null, ?string $referer = null): Comment
    {
        $context = $this->moderationService->moderate(
            $data['content'],
            $identity,
            $post,
            $ipAddress,
            $userAgent,
            $referer
        );

        return DB::transaction(function () use ($post, $identity, $data, $context, $ipAddress, $userAgent) {
            $commentData = $this->buildCommentData($identity, $context, $ipAddress, $userAgent);
            $commentData['post_id'] = $post->id;
            
            if (isset($data['guest_name'])) {
                $commentData['guest_name'] = $data['guest_name'];
            }
            if (isset($data['guest_email'])) {
                $commentData['guest_email'] = $data['guest_email'];
            }
            
            return Comment::create($commentData);
        });
    }

    /**
     * Reply to an existing comment.
     */
    public function replyTo(Comment $target, array $identity, array $data, ?string $ipAddress = null, ?string $userAgent = null, ?string $referer = null): Comment
    {
        $post = $target->post;
        $content = $data['content'];

        $parentId = $target->id;
        $depth = $target->depth + 1;

        if ($target->depth >= self::MAX_DEPTH) {
            $parentId = $target->parent_id;
            $depth = self::MAX_DEPTH;
            
            // Prepend username if replying to a max-depth comment and target is a registered user
            if ($target->author && $target->author->username) {
                $mentionPrefix = '@' . $target->author->username . ' ';
                if (!str_starts_with($content, $mentionPrefix)) {
                    $content = $mentionPrefix . $content;
                }
            }
        }

        $context = $this->moderationService->moderate(
            $content,
            $identity,
            $post,
            $ipAddress,
            $userAgent,
            $referer
        );

        return DB::transaction(function () use ($target, $post, $parentId, $depth, $identity, $data, $context, $ipAddress, $userAgent) {
            $commentData = $this->buildCommentData($identity, $context, $ipAddress, $userAgent);
            $commentData['post_id'] = $post->id;
            $commentData['parent_id'] = $parentId;
            $commentData['depth'] = $depth;
            
            if (isset($data['guest_name'])) {
                $commentData['guest_name'] = $data['guest_name'];
            }
            if (isset($data['guest_email'])) {
                $commentData['guest_email'] = $data['guest_email'];
            }
            
            $comment = Comment::create($commentData);
            
            return $comment;
        });
    }

    private function buildCommentData(array $identity, \App\Services\Moderation\ModerationContext $context, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $isGuest = $identity['type'] === 'guest';
        
        return [
            'identity_key' => $identity['key'],
            'author_id' => $isGuest ? null : $identity['user_id'],
            'created_as_guest' => $isGuest,
            'content' => $context->normalizedContent,
            'status' => $context->status->value,
            'moderation_score' => $context->score,
            'moderation_flags' => empty($context->flags) ? null : json_encode($context->flags),
            'moderated_at' => $context->submittedAt,
            'guest_ip' => $ipAddress,
            'guest_user_agent' => $userAgent,
        ];
    }
}
