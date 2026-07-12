<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CommentRateLimiter
{
    /**
     * Increment the attempts and return true if the limit is exceeded.
     */
    public function tooManyAttempts(array $identity): bool
    {
        $key = 'comment_rate_limit:' . $identity['key'];
        
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            return true;
        }

        Cache::add($key, 0, now()->addMinutes(1));
        Cache::increment($key);

        return false;
    }
}
