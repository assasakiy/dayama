<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\User;

use App\Models\ReadingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReadingHistoryController
{
    public function index(Request $request): Response
    {
        Gate::authorize('reading_history.view.own');

        $userId = auth()->id();

        $history = ReadingHistory::with(['post' => fn ($q) => $q->with(['author', 'primaryCategory'])])
            ->where('user_id', $userId)
            ->orderByDesc('last_read_at')
            ->paginate(20)
            ->through(fn ($entry) => [
                'id'           => $entry->id,
                'read_count'   => $entry->read_count,
                'first_read_at' => $entry->first_read_at?->toISOString(),
                'last_read_at' => $entry->last_read_at?->toISOString(),
                'last_read_human' => $entry->last_read_at?->diffForHumans(),
                'post'         => $entry->post ? [
                    'id'           => $entry->post->id,
                    'title'        => $entry->post->title,
                    'slug'         => $entry->post->slug,
                    'excerpt'      => $entry->post->excerpt,
                    'cover'        => $entry->post->cover_url ?? null,
                    'reading_time' => $entry->post->reading_time,
                    'published_at' => $entry->post->published_at?->toISOString(),
                    'author'       => $entry->post->author ? [
                        'name'       => $entry->post->author->name,
                        'username'   => $entry->post->author->username,
                        'avatar_url' => $entry->post->author->avatar_url ?? null,
                    ] : null,
                    'category'     => $entry->post->primaryCategory ? [
                        'name' => $entry->post->primaryCategory->name,
                        'slug' => $entry->post->primaryCategory->slug,
                    ] : null,
                ] : null,
            ]);

        return Inertia::render('User/History/Index', [
            'history' => $history,
        ]);
    }
}
