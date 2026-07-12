<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\Api\V1\PostCollection;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(Request $request): PostCollection
    {
        $identity = \App\Services\IdentityService::current();

        $query = Post::where('status', 'published')
            ->with(['author', 'category', 'tags'])
            ->withCount(['reactions', 'comments'])
            ->withExists(['reactions as is_liked' => fn ($q) => $q->where('identity_key', $identity['key'])])
            ->withExists(['bookmarks as is_bookmarked' => fn ($q) => $q->where('identity_key', $identity['key'])]);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        if ($request->filled('author')) {
            $query->whereHas('author', fn ($q) => $q->where('username', $request->author));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$s}%")->orWhere('excerpt', 'like', "%{$s}%"));
        }

        $sortField = in_array($request->sort, ['title', 'published_at', 'views_count']) ? $request->sort : 'published_at';
        $sortDir = $request->direction === 'asc' ? 'asc' : 'desc';

        $posts = $query->orderBy($sortField, $sortDir)->paginate($request->per_page ?? 12);

        return new PostCollection($posts);
    }

    public function show(Post $post): PostResource
    {
        abort_unless($post->status === 'published', 404);
        $identity = \App\Services\IdentityService::current();

        $post->load(['author', 'category', 'tags']);
        $post->loadCount(['reactions', 'comments']);
        $post->is_liked = $post->reactions()->where('identity_key', $identity['key'])->exists();
        $post->is_bookmarked = $post->bookmarks()->where('identity_key', $identity['key'])->exists();
        
        $post->increment('views_count');

        return new PostResource($post);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::make($request->validated());
        $post->author_id = $request->user()->id;
        $post->save();

        if ($tags = $request->input('tags')) {
            $post->tags()->sync($tags);
        }

        return response()->json(new PostResource($post->fresh(['author', 'category', 'tags'])), 201);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $post->update($request->validated());

        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags', []));
        }

        return response()->json(new PostResource($post->fresh(['author', 'category', 'tags'])));
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted.'], 200);
    }
}
