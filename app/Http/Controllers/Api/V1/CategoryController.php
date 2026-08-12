<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use Modules\CMS\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('posts')
            ->whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        $category->load(['children', 'posts' => fn ($q) => $q->where('status', 'published')->latest()]);

        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json(new CategoryResource($category), 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json(new CategoryResource($category->fresh()));
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted.'], 200);
    }
}
