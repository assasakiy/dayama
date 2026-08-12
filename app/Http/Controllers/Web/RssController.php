<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Modules\CMS\Models\Post;

class RssController
{
    public function __invoke()
    {
        $posts = Post::where('status', 'published')
            ->with(['author', 'primaryCategory'])
            ->latest('published_at')
            ->take(50)
            ->get();

        return response()->view('web.seo.blog.rss', compact('posts'), 200)
            ->header('Content-Type', 'application/rss+xml');
    }
}
