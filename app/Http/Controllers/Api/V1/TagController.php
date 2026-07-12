<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreTagRequest;
use App\Http\Requests\Api\V1\UpdateTagRequest;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::withCount('posts')
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return TagResource::collection($tags);
    }

    public function show(Tag $tag): TagResource
    {
        $tag->load(['posts' => fn ($q) => $q->where('status', 'published')->latest()->limit(10)]);

        return new TagResource($tag);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::create($request->validated());

        return response()->json(new TagResource($tag), 201);
    }

    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $tag->update($request->validated());

        return response()->json(new TagResource($tag->fresh()));
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(['message' => 'Tag deleted.'], 200);
    }
}
