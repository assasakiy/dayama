<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController
{
    public function __invoke(Request $request): View
    {
        $query = $request->query('q', '');

        $posts = collect();
        if (strlen($query) >= 2) {
            $posts = Post::where('status', 'published')
                ->where(function ($q) use ($query): void {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%");
                })
                ->with(['author', 'primaryCategory'])
                ->latest('published_at')
                ->paginate(12);
        }

        return view('web.pages.search', compact('posts', 'query'));
    }
}
