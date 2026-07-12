<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class SitemapController
{
    public function __invoke()
    {
        return response()->view('web.seo.sitemap-index', [], 200)
            ->header('Content-Type', 'application/xml');
    }


    public function posts()
    {
        $posts = Post::where('status', 'published')->latest('published_at')->get();

        return response()->view('web.seo.sitemap-posts', compact('posts'), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function categories()
    {
        $categories = Category::whereHas('posts', fn ($q) => $q->where('status', 'published'))->get();

        return response()->view('web.seo.sitemap-categories', compact('categories'), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function tags()
    {
        $tags = Tag::whereHas('posts', fn ($q) => $q->where('status', 'published'))->get();

        return response()->view('web.seo.sitemap-tags', compact('tags'), 200)
            ->header('Content-Type', 'application/xml');
    }
}