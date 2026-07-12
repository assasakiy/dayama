<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Bookmark;
use App\Models\ReadingHistory;
use App\Models\Reaction;
use App\Models\PostView;

class IdentityMigrationService
{
    /**
     * Merge guest data to user data after login.
     *
     * @param string $visitorToken
     * @param string|int $userId
     */
    public function mergeGuestIdentity(string $visitorToken, $userId): void
    {
        $guestIdentityKey = "guest:{$visitorToken}";
        $userIdentityKey = "user:{$userId}";

        DB::transaction(function () use ($guestIdentityKey, $userIdentityKey, $userId) {
            $this->mergeBookmarks($guestIdentityKey, $userIdentityKey, $userId);
            $this->mergeReadingHistory($guestIdentityKey, $userIdentityKey, $userId);
            $this->mergeReactions($guestIdentityKey, $userIdentityKey, $userId);
            $this->mergeViews($guestIdentityKey, $userIdentityKey, $userId);
        });
    }

    protected function mergeBookmarks(string $guestKey, string $userKey, $userId): void
    {
        $guestBookmarks = Bookmark::where('identity_key', $guestKey)->get();

        foreach ($guestBookmarks as $guestBookmark) {
            $userBookmark = Bookmark::where('post_id', $guestBookmark->post_id)
                ->where('identity_key', $userKey)
                ->first();

            if ($userBookmark) {
                // Deduplicate: user already bookmarked, delete guest row
                $guestBookmark->delete();
            } else {
                // No conflict, migrate to user
                $guestBookmark->update([
                    'identity_key' => $userKey,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    protected function mergeReadingHistory(string $guestKey, string $userKey, $userId): void
    {
        $guestHistories = ReadingHistory::where('identity_key', $guestKey)->get();

        foreach ($guestHistories as $guestHistory) {
            $userHistory = ReadingHistory::where('post_id', $guestHistory->post_id)
                ->where('identity_key', $userKey)
                ->first();

            if ($userHistory) {
                // Merge logic
                $userHistory->update([
                    'read_count' => $userHistory->read_count + $guestHistory->read_count,
                    'first_read_at' => min($userHistory->first_read_at, $guestHistory->first_read_at),
                    'last_read_at' => max($userHistory->last_read_at, $guestHistory->last_read_at),
                ]);
                $guestHistory->delete();
            } else {
                // No conflict
                $guestHistory->update([
                    'identity_key' => $userKey,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    protected function mergeReactions(string $guestKey, string $userKey, $userId): void
    {
        $guestReactions = Reaction::where('identity_key', $guestKey)->get();

        foreach ($guestReactions as $guestReaction) {
            $userReaction = Reaction::where('post_id', $guestReaction->post_id)
                ->where('identity_key', $userKey)
                ->first();

            if ($userReaction) {
                // Last-write-wins based on updated_at
                if ($guestReaction->updated_at > $userReaction->updated_at) {
                    $userReaction->update([
                        'type' => $guestReaction->type,
                        'updated_at' => $guestReaction->updated_at,
                    ]);
                }
                $guestReaction->delete();
            } else {
                // No conflict
                $guestReaction->update([
                    'identity_key' => $userKey,
                    'user_id' => $userId,
                ]);
            }
        }
    }

    protected function mergeViews(string $guestKey, string $userKey, $userId): void
    {
        $guestViews = PostView::where('identity_key', $guestKey)->get();

        foreach ($guestViews as $guestView) {
            // View constraint is UNIQUE(post_id, identity_key, view_date)
            $userView = PostView::where('post_id', $guestView->post_id)
                ->where('identity_key', $userKey)
                ->where('view_date', $guestView->view_date)
                ->first();

            if ($userView) {
                // Deduplicate: user already viewed on this date, delete guest row
                $guestView->delete();
            } else {
                // No conflict
                $guestView->update([
                    'identity_key' => $userKey,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
