<?php

namespace App\Services;

use Modules\CMS\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostMetricsService
{
    public function recordView(Post $post): void
    {
        $key = "post_view_{$post->id}_" . request()->ip();
        $lock = Cache::lock($key, 10);

        if ($lock->get()) {
            try {
                DB::transaction(function () use ($post) {
                    $post->increment('views_count');
                });
            } finally {
                $lock->release();
            }
        }
    }
}
