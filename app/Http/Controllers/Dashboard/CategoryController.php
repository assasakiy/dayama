<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Dashboard\StoreCategoryRequest;
use App\Http\Requests\Dashboard\UpdateCategoryRequest;
use Modules\CMS\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Category::class);
        return Inertia::render('Categories/Index', [
            'categories' => \App\Http\Resources\Dashboard\CategoryResource::collection(
                Category::with(['parent:id,name', 'creator:id,name', 'updater:id,name'])->latest()->get()
            )->resolve(),
            'parentCategories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except(['image', 'image_media_id'])->toArray();
        $category = Category::create($data);

        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')
                ->toMediaCollection('image');
        } elseif ($request->filled('image_media_id')) {
            $media = \Modules\Core\Models\Media::find($request->image_media_id);
            if ($media) {
                $media->copy($category, 'image');
            }
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Category created.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = collect($request->validated())->except(['image', 'image_media_id', 'remove_image'])->toArray();
        $category->update($data);

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('image');
            $category->addMediaFromRequest('image')->toMediaCollection('image');
        } elseif ($request->filled('image_media_id')) {
            $category->clearMediaCollection('image');
            $media = \Modules\Core\Models\Media::find($request->image_media_id);
            if ($media) {
                $media->copy($category, 'image');
            }
        } elseif ($request->boolean('remove_image')) {
            $category->clearMediaCollection('image');
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);
        $category->delete();

        return redirect()->route('dashboard.categories.index')->with('success', 'Category deleted.');
    }
}
