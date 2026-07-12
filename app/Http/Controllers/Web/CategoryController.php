<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController
{
    public function index(): View
    {
        $categories = Category::where('is_visible', true)
            ->orderBy('name')
            ->get();

        return view('web.pages.categories', compact('categories'));
    }

    public function show(Request $request, Category $category): View
    {
        $posts = $category->posts()
            ->where('status', 'published')
            ->with(['author', 'tags', 'primaryCategory'])
            ->latest('published_at')
            ->paginate(9);

        return view('web.pages.category', compact('category', 'posts'));
    }
}
