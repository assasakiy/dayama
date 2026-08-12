<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Modules\CMS\Models\Post;
use Modules\CMS\Models\Category;
use Illuminate\View\View;

class HomeController
{
    public function __invoke(): View
    {
        $featured = Post::where('status', 'published')
            ->where('is_featured', true)
            ->with(['author', 'primaryCategory', 'tags'])
            ->latest('published_at')
            ->take(5)
            ->get();

        $latest = Post::where('status', 'published')
            ->with(['author', 'primaryCategory'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $popular = Post::where('status', 'published')
            ->with(['author'])
            ->orderBy('views_count', 'desc')
            ->take(4)
            ->get();

        $categories = Category::whereNull('parent_id')
            ->withCount('posts')
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->orderBy('sort_order')
            ->get();

        return view('web.pages.home', compact('featured', 'latest', 'popular', 'categories'));
    }
}
