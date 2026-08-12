<?php

namespace App\Services;

use Modules\CMS\Models\Category;

class NavigationService
{
    public function getCategories(): iterable
    {
        return Category::withCount('posts')
            ->having('posts_count', '>', 0)
            ->latest('posts_count')
            ->take(8)
            ->get();
    }
}
