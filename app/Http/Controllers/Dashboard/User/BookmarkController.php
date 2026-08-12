<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\User;

use Modules\CMS\Models\Bookmark;
use Modules\CMS\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkController
{
    public function index(Request $request): Response
    {
        Gate::authorize('bookmarks.view.own');

        $userId = auth()->id();

        $bookmarks = Bookmark::with(['post' => fn ($q) => $q->with(['author', 'primaryCategory'])])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(20)
            ->through(fn ($bookmark) => [
                'id'         => $bookmark->id,
                'created_at' => $bookmark->created_at?->toISOString(),
                'post'       => $bookmark->post ? [
                    'id'           => $bookmark->post->id,
                    'title'        => $bookmark->post->title,
                    'slug'         => $bookmark->post->slug,
                    'excerpt'      => $bookmark->post->excerpt,
                    'cover'        => $bookmark->post->cover_url ?? null,
                    'reading_time' => $bookmark->post->reading_time,
                    'published_at' => $bookmark->post->published_at?->toISOString(),
                    'author'       => $bookmark->post->author ? [
                        'name'       => $bookmark->post->author->name,
                        'username'   => $bookmark->post->author->username,
                        'avatar_url' => $bookmark->post->author->avatar_url ?? null,
                    ] : null,
                    'category'     => $bookmark->post->primaryCategory ? [
                        'name' => $bookmark->post->primaryCategory->name,
                        'slug' => $bookmark->post->primaryCategory->slug,
                    ] : null,
                ] : null,
            ]);

        return Inertia::render('User/Bookmarks/Index', [
            'bookmarks' => $bookmarks,
        ]);
    }

    public function destroy(Request $request, string $postId): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('bookmarks.view.own');

        Bookmark::where('user_id', auth()->id())
            ->where('post_id', $postId)
            ->delete();

        return back()->with('success', 'Bookmark dihapus.');
    }
}
