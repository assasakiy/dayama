<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Dashboard\StoreTagRequest;
use App\Http\Requests\Dashboard\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TagController
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Tag::class);
        return Inertia::render('Tags/Index', [
            'tags' => \App\Http\Resources\Dashboard\TagResource::collection(
                Tag::with(['creator:id,name', 'updater:id,name'])->latest()->get()
            )->resolve(),
        ]);
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag created.');
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validated());

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('delete', $tag);
        $tag->delete();

        return redirect()->route('dashboard.tags.index')->with('success', 'Tag deleted.');
    }
}
