<?php

namespace App\Observers;

use Modules\CMS\Models\PostView;

class PostViewObserver
{
    /**
     * Handle the PostView "created" event.
     */
    public function created(PostView $postView): void
    {
        if ($postView->is_unique) {
            $postView->post()->increment('views_count');
        }
    }

    /**
     * Handle the PostView "updated" event.
     */
    public function updated(PostView $postView): void
    {
        //
    }

    /**
     * Handle the PostView "deleted" event.
     */
    public function deleted(PostView $postView): void
    {
        //
    }

    /**
     * Handle the PostView "restored" event.
     */
    public function restored(PostView $postView): void
    {
        //
    }

    /**
     * Handle the PostView "force deleted" event.
     */
    public function forceDeleted(PostView $postView): void
    {
        //
    }
}
