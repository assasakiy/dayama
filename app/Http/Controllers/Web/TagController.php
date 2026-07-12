<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController
{
    public function index(): View
    {
        $tags = Tag::orderBy('name')->get();

        return view('web.pages.tags', compact('tags'));
    }

    public function show(Request $request, Tag $tag): View
    {
        $posts = $tag->posts()
            ->where('status', 'published')
            ->with(['author', 'primaryCategory'])
            ->latest('published_at')
            ->paginate(9);

        return view('web.pages.tag', compact('tag', 'posts'));
    }
}
