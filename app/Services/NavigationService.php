<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public function getCategories(): iterable
    {
        return Cache::remember('nav_categories', 3600, function () {
            return Category::withCount('posts')
                ->having('posts_count', '>', 0)
                ->latest('posts_count')
                ->take(8)
                ->get();
        });
    }
}
