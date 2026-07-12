<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\IdentityService;
use App\Services\CrawlerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\UniqueConstraintViolationException;

class PostController
{
    public function show(Request $request, Post $post): View
    {
        abort_unless($post->status === 'published', 404);

        $post->load(['author', 'primaryCategory', 'tags', 'categories']);

        $related = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post): void {
                if ($post->categories->isNotEmpty()) {
                    $q->whereHas('categories', function ($query) use ($post) {
                        $query->whereIn('categories.id', $post->categories->pluck('id'));
                    });
                }
                if ($post->tags->isNotEmpty()) {
                    $q->orWhereHas('tags', function ($query) use ($post) {
                        $query->whereIn('tags.id', $post->tags->pluck('id'));
                    });
                }
            })
            ->with(['author', 'primaryCategory'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $identity = IdentityService::current();
        $isLiked = $post->reactions()->where('identity_key', $identity['key'])->exists();
        $isBookmarked = $post->bookmarks()->where('identity_key', $identity['key'])->exists();
        $post->loadCount(['reactions', 'comments']);

        if (! CrawlerService::isCrawler($request->userAgent())) {
            $identity = IdentityService::current();
            $viewDate = now()->toDateString();
            $cacheKey = "post_view_{$post->id}_{$identity['key']}_{$viewDate}";

            // Atomic unique check using Cache
            if (Cache::add($cacheKey, true, now()->addHours(24))) {
                try {
                    \App\Models\PostView::create([
                        'post_id' => $post->id,
                        'user_id' => $identity['user_id'],
                        'identity_key' => $identity['key'],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'referrer' => $request->header('referer'),
                        'is_unique' => true,
                        'view_date' => $viewDate,
                        'viewed_at' => now(),
                    ]);
                } catch (UniqueConstraintViolationException $e) {
                    // Fallback in case cache was cleared but DB constraint still blocks it
                }
            }

            // Always update reading history regardless of the view Cache lock
            app(\App\Services\ReadingHistoryService::class)->recordRead($post, $identity);
        }

        return view('web.pages.post', compact('post', 'related', 'isLiked', 'isBookmarked'));
    }
}
