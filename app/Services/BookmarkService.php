<?php

namespace App\Services;

use Modules\CMS\Models\Bookmark;
use Modules\CMS\Models\Post;

class BookmarkService
{
    /**
     * Create or delete a bookmark for a specific post and identity.
     * Returns true if bookmarked, false if unbookmarked.
     */
    public function toggle(Post $post, array $identity): bool
    {
        $existing = Bookmark::where('post_id', $post->id)
            ->where('identity_key', $identity['key'])
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        Bookmark::create([
            'post_id' => $post->id,
            'identity_key' => $identity['key'],
            'user_id' => $identity['user_id'] ?? null,
        ]);

        return true;
    }

    /**
     * Check if a post is bookmarked by a specific identity.
     */
    public function isBookmarked(Post $post, array $identity): bool
    {
        return Bookmark::where('post_id', $post->id)
            ->where('identity_key', $identity['key'])
            ->exists();
    }

    /**
     * Retrieve bookmarks for a specific identity, ordered by latest.
     */
    public function bookmarksForIdentity(array $identity, int $limit = 20)
    {
        return Bookmark::with('post')
            ->where('identity_key', $identity['key'])
            ->latest()
            ->paginate($limit);
    }
}
