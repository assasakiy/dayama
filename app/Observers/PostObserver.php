<?php

namespace App\Observers;

use Modules\CMS\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "saving" event.
     */
    public function saving(Post $post): void
    {
        if ($post->isDirty('content')) {
            $wordCount = $post->content ? str_word_count(strip_tags($post->content)) : 0;
            $post->word_count = $wordCount;
            $post->reading_time = max(1, (int) ceil($wordCount / 200));
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        Post::syncCounts();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        // We will move revision creation here in a more robust setup,
        // but for now we sync counts if categories/tags changed.
        // syncCounts handles global counts, but we can call it to be safe.
        // Actually, syncCounts is expensive to call on every update if not needed.
        // For now, let's keep it simple as the user suggested.
        // If status changed to/from published, we should sync counts!
        if ($post->isDirty('status') || $post->isDirty('primary_category_id')) {
            Post::syncCounts();
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        Post::syncCounts();
    }

    /**
     * Handle the Post "restoring" event.
     */
    public function restoring(Post $post): void
    {
        // Handled by HasUserstamps
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        Post::syncCounts();
    }

    /**
     * Handle the Post "forceDeleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        Post::syncCounts();
    }
}
