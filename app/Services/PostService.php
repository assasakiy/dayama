<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function getFeatured(int $limit = 3): iterable
    {
        return Post::published()->featured()->with(['author', 'category'])->latest('published_at')->take($limit)->get();
    }

    public function getLatest(int $limit = 6): iterable
    {
        return Post::published()->with(['author', 'category'])->latest('published_at')->take($limit)->get();
    }

    public function getPopular(int $limit = 4): iterable
    {
        return Post::published()->popular()->with(['author', 'category'])->take($limit)->get();
    }

    public function getTrending(int $limit = 5): iterable
    {
        return Post::published()->trending()->with(['author', 'category'])->take($limit)->get();
    }

    public function getFiltered(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Post::published()->with(['author', 'category']);

        if (!empty($filters['category'])) {
            $query->whereHas('category', fn($q) => $q->where('slug', $filters['category']));
        }
        if (!empty($filters['tag'])) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $filters['tag']));
        }
        if (!empty($filters['author'])) {
            $query->whereHas('author', fn($q) => $q->where('username', $filters['author']));
        }

        $sort = $filters['sort'] ?? 'recent';
        if ($sort === 'popular') {
            $query->popular();
        } else {
            $query->latest('published_at');
        }

        return $query->paginate($perPage);
    }
}
