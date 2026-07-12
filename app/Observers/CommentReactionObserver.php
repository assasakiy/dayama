<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CommentReaction;

class CommentReactionObserver
{
    public function created(CommentReaction $reaction): void
    {
        $this->syncLikesCount($reaction->comment_id);
    }

    public function deleted(CommentReaction $reaction): void
    {
        $this->syncLikesCount($reaction->comment_id);
    }

    /**
     * Full-recount from source of truth — correctness over micro-performance.
     * If needed later, this can be moved to a queued job without changing callers.
     */
    private function syncLikesCount(string $commentId): void
    {
        $count = CommentReaction::where('comment_id', $commentId)->count();

        \App\Models\Comment::withoutGlobalScopes()
            ->where('id', $commentId)
            ->update(['likes_count' => $count]);
    }
}
