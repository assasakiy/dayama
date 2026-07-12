<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        if ($comment->status === \App\Enums\CommentStatus::Published->value) {
            $comment->parent?->increment('replies_count');
            event(new \App\Events\CommentPublished($comment));
        }
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        if ($comment->isDirty('status')) {
            $was = $comment->getOriginal('status');
            $now = $comment->status;

            if ($was !== \App\Enums\CommentStatus::Published->value && $now === \App\Enums\CommentStatus::Published->value) {
                $comment->parent?->increment('replies_count');
                event(new \App\Events\CommentPublished($comment));
            } elseif ($was === \App\Enums\CommentStatus::Published->value && $now !== \App\Enums\CommentStatus::Published->value) {
                $comment->parent?->decrement('replies_count');
            }
        }
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        // Soft delete does not decrement replies_count
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        // Restoring a soft deleted comment doesn't trigger replies_count changes if it was already published,
        // because it never decremented on soft delete.
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        if ($comment->status === \App\Enums\CommentStatus::Published->value) {
            $comment->parent?->decrement('replies_count');
        }
    }
}
