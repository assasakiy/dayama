<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Modules\CMS\Models\Category;
use Modules\CMS\Models\Comment;
use Modules\CMS\Models\Post;
use Modules\CMS\Models\Tag;
use Modules\Core\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'posts' => Post::count(),
                'categories' => Category::count(),
                'tags' => Tag::count(),
                'comments' => Comment::count(),
                'users' => User::count(),
                'published_posts' => Post::where('status', 'published')->count(),
                'draft_posts' => Post::where('status', 'draft')->count(),
                'pending_comments' => Comment::where('status', 'pending')->count(),
            ],
            'recent_posts' => Post::with(['author', 'primaryCategory'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'status' => $p->status,
                    'author' => $p->author?->only('name'),
                    'category' => $p->primaryCategory?->only('name'),
                    'thumbnail_url' => $p->thumbnail_url,
                    'published_at' => $p->published_at,
                ]),
            'recent_comments' => Comment::with(['author', 'post'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
