<?php

namespace App\Observers;

use App\Models\Reaction;
use Illuminate\Support\Facades\DB;

class ReactionObserver
{
    /**
     * Handle the Reaction "created" event.
     */
    public function created(Reaction $reaction): void
    {
        $this->updatePostReactions($reaction);
    }

    /**
     * Handle the Reaction "updated" event.
     */
    public function updated(Reaction $reaction): void
    {
        if ($reaction->isDirty('type')) {
            $this->updatePostReactions($reaction);
        }
    }

    /**
     * Handle the Reaction "deleted" event.
     */
    public function deleted(Reaction $reaction): void
    {
        $this->updatePostReactions($reaction);
    }

    /**
     * Handle the Reaction "restored" event.
     */
    public function restored(Reaction $reaction): void
    {
        $this->updatePostReactions($reaction);
    }

    /**
     * Handle the Reaction "force deleted" event.
     */
    public function forceDeleted(Reaction $reaction): void
    {
        $this->updatePostReactions($reaction);
    }

    protected function updatePostReactions(Reaction $reaction): void
    {
        $post = $reaction->post;
        if (! $post) {
            return;
        }

        // NOTE:
        // This observer recalculates all reactions.
        // When traffic becomes high, move this logic into queued jobs.
        // Correctness > Micro Performance for the initial phase.
        
        $totals = DB::table('reactions')
            ->select('type', DB::raw('count(*) as count'))
            ->where('post_id', $post->id)
            ->groupBy('type')
            ->get();

        $breakdown = [];
        $totalReactions = 0;

        foreach ($totals as $row) {
            $breakdown[$row->type] = (int) $row->count;
            $totalReactions += (int) $row->count;
        }

        $post->reactions_breakdown = empty($breakdown) ? null : $breakdown;
        $post->reactions_count = $totalReactions;
        
        $post->saveQuietly();
    }
}
