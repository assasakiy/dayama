<?php

namespace App\Services;

use Modules\CMS\Models\Post;
use Modules\CMS\Models\ReadingHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReadingHistoryService
{
    /**
     * Record a post read atomically using ON DUPLICATE KEY UPDATE.
     */
    public function recordRead(Post $post, array $identity): void
    {
        $id = Str::uuid()->toString();
        $now = now()->toDateTimeString();

        DB::statement(
            "INSERT INTO reading_histories (id, post_id, identity_key, user_id, first_read_at, last_read_at, read_count, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?) 
             ON DUPLICATE KEY UPDATE 
                 read_count = read_count + 1,
                 last_read_at = VALUES(last_read_at),
                 updated_at = VALUES(updated_at)",
            [
                $id,
                $post->id,
                $identity['key'],
                $identity['user_id'] ?? null,
                $now, // first_read_at
                $now, // last_read_at
                $now, // created_at
                $now, // updated_at
            ]
        );
    }

    /**
     * Retrieve recent reading history for a specific identity.
     */
    public function recentHistory(array $identity, int $limit = 20)
    {
        return ReadingHistory::with('post')
            ->where('identity_key', $identity['key'])
            ->orderByDesc('last_read_at')
            ->paginate($limit);
    }

    /**
     * Clear reading history for a specific identity.
     */
    public function clearHistory(array $identity): void
    {
        ReadingHistory::where('identity_key', $identity['key'])->delete();
    }
}
