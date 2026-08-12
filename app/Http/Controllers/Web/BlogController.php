<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Modules\CMS\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController
{
    public function __invoke(Request $request): View
    {
        $posts = Post::where('status', 'published')
            ->with(['author', 'primaryCategory', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('web.pages.blog', compact('posts'));
    }

    public function trending(): View
    {
        $posts = Post::where('status', 'published')
            ->with(['author', 'primaryCategory', 'tags'])
            ->orderBy('views_count', 'desc')
            ->paginate(9);

        return view('web.pages.trending', compact('posts'));
    }
}
