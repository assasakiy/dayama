<?php

namespace App\Services;

use Modules\CMS\Models\Post;
use Modules\CMS\Models\Category;
use Modules\Core\Models\User;
use Illuminate\Support\Collection;

class SearchService
{
    public function search(string $query, string $type = 'all'): Collection
    {
        $results = collect();

        if ($type === 'all' || $type === 'articles') {
            $posts = Post::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%")
                      ->orWhere('body', 'like', "%{$query}%");
                })
                ->with(['author', 'category'])
                ->latest('published_at')
                ->take(10)
                ->get()
                ->map(fn($p) => ['type' => 'article', 'data' => $p]);
            $results = $results->concat($posts);
        }

        if ($type === 'all' || $type === 'categories') {
            $categories = Category::where('name', 'like', "%{$query}%")
                ->take(5)
                ->get()
                ->map(fn($c) => ['type' => 'category', 'data' => $c]);
            $results = $results->concat($categories);
        }

        if ($type === 'all' || $type === 'authors') {
            $authors = User::where('name', 'like', "%{$query}%")
                ->take(5)
                ->get()
                ->map(fn($u) => ['type' => 'author', 'data' => $u]);
            $results = $results->concat($authors);
        }

        return $results;
    }
}
