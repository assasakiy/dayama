<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController
{
    public function index(): View
    {
        $authors = User::whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get();

        return view('web.pages.authors', compact('authors'));
    }

    public function show(Request $request, User $user): View
    {
        $posts = $user->posts()
            ->where('status', 'published')
            ->with(['primaryCategory', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('web.pages.author', compact('user', 'posts'));
    }
}
