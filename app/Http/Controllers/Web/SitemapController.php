<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Modules\CMS\Models\Category;
use Modules\CMS\Models\Post;
use Modules\CMS\Models\Tag;

class SitemapController
{
    public function __invoke()
    {
        return response()->view('web.seo.blog.index', [], 200)
            ->header('Content-Type', 'application/xml');
    }

    public function blogXsl()
    {
        return response()->view('web.seo.blog.xsl', [], 200)
            ->header('Content-Type', 'text/xsl');
    }

    public function pages()
    {
        return response()->view('web.seo.blog.pages', [], 200)
            ->header('Content-Type', 'application/xml');
    }

    public function landingIndex()
    {
        return response()->view('web.seo.landing.index', [], 200)
            ->header('Content-Type', 'application/xml');
    }

    public function landingXsl()
    {
        return response()->view('web.seo.landing.xsl', [], 200)
            ->header('Content-Type', 'text/xsl');
    }

    public function landingSection($section)
    {
        return response()->view("web.seo.landing.{$section}", [], 200)
            ->header('Content-Type', 'application/xml');
    }


    public function posts()
    {
        $posts = Post::where('status', 'published')->latest('published_at')->get();

        return response()->view('web.seo.blog.posts', compact('posts'), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function categories()
    {
        $categories = Category::whereHas('posts', fn ($q) => $q->where('status', 'published'))->get();

        return response()->view('web.seo.blog.categories', compact('categories'), 200)
            ->header('Content-Type', 'application/xml');
    }

    public function tags()
    {
        $tags = Tag::whereHas('posts', fn ($q) => $q->where('status', 'published'))->get();

        return response()->view('web.seo.blog.tags', compact('tags'), 200)
            ->header('Content-Type', 'application/xml');
    }
}