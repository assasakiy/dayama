<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Dashboard\StorePostRequest;
use App\Http\Requests\Dashboard\UpdatePostRequest;
use Modules\CMS\Models\Category;
use Modules\CMS\Models\Post;
use Modules\CMS\Models\PostRevision;
use Modules\CMS\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Post::class);

        session()->put('posts_index_url', $request->fullUrl());

        $query = Post::with(['author', 'primaryCategory', 'tags']);

        if ($request->filled('status')) {
            if ($request->status === 'trash') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $request->status);
            }
        }

        return Inertia::render('Posts/Index', [
            'posts' => $query->latest()->paginate(15)
                ->through(fn ($post) => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'status' => $post->status,
                    'author' => $post->author?->only('name'),
                    'category' => $post->primaryCategory?->only('name'),
                    'thumbnail_url' => $post->thumbnail_url,
                    'created_at' => $post->created_at,
                ]),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Post::class);
        return Inertia::render('Posts/Form', [
            'post' => null,
            'categories' => Category::with('parent:id,name')->orderBy('name')->get(['id', 'name', 'parent_id'])->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'parent_name' => $c->parent ? $c->parent->name : null,
            ]),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except(['thumbnail', 'tags', 'categories', 'thumbnail_media_id'])->toArray();
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_values(array_filter(array_map('trim', explode(',', $data['meta_keywords']))));
        }
        $post = Post::make($data);
        $post->author_id = $request->user()->id;
        
        $slug = \Illuminate\Support\Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }
        $post->slug = $slug;
        
        if ($post->status === 'published' && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();
        
        $post->content = $this->processContentMedia($post, $post->content);
        $post->save();

        if ($tags = $request->input('tags')) {
            $post->tags()->sync($tags);
        }

        $categories = $request->input('categories', []);
        if ($post->primary_category_id && !in_array($post->primary_category_id, $categories)) {
            $categories[] = $post->primary_category_id;
        }
        $post->categories()->sync($categories);

        if ($request->hasFile('thumbnail')) {
            $post->addMediaFromRequest('thumbnail')
                ->toMediaCollection('thumbnail');
        } elseif ($request->filled('thumbnail_media_id')) {
            $media = \Modules\Core\Models\Media::find($request->thumbnail_media_id);
            if ($media) {
                $media->copy($post, 'thumbnail');
            }
        }

        $redirectUrl = session()->pull('posts_index_url', route('dashboard.posts.index'));
        
        return redirect($redirectUrl)->with('success', 'Post created.');
    }

    public function edit(Post $post): Response
    {
        Gate::authorize('update', $post);
        $post->load(['tags', 'categories']);

        return Inertia::render('Posts/Form', [
            'post' => array_merge($post->toArray(), [
                'thumbnail_url' => $post->thumbnail_url,
            ]),
            'categories' => Category::with('parent:id,name')->orderBy('name')->get(['id', 'name', 'parent_id'])->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'parent_name' => $c->parent ? $c->parent->name : null,
            ]),
            'tags' => Tag::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = collect($request->validated())->except(['thumbnail', 'tags', 'categories', 'thumbnail_media_id'])->toArray();
        if (isset($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = array_values(array_filter(array_map('trim', explode(',', $data['meta_keywords']))));
        }
        
        if (isset($data['status']) && $data['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->fill($data);
        
        $changedFields = array_keys($post->getDirty());
        
        if ($request->has('tags')) {
            $currentTags = $post->tags->pluck('id')->toArray();
            $newTags = $request->input('tags', []);
            if (array_diff($currentTags, $newTags) || array_diff($newTags, $currentTags)) {
                $changedFields[] = 'tags';
            }
        }

        if ($request->has('categories') || $request->has('primary_category_id')) {
            $currentCategories = $post->categories->pluck('id')->toArray();
            $newCategories = $request->input('categories', []);
            if (isset($data['primary_category_id']) && !in_array($data['primary_category_id'], $newCategories)) {
                $newCategories[] = (int) $data['primary_category_id'];
            } elseif ($post->primary_category_id && !in_array($post->primary_category_id, $newCategories)) {
                $newCategories[] = $post->primary_category_id;
            }
            
            if (array_diff($currentCategories, $newCategories) || array_diff($newCategories, $currentCategories)) {
                if (!in_array('categories', $changedFields)) {
                    $changedFields[] = 'categories';
                }
            }
        }
        
        if ($request->hasFile('thumbnail') || $request->filled('thumbnail_media_id')) {
            $changedFields[] = 'thumbnail';
        }
        
        $changeSummary = !empty($changedFields) ? 'Updated: ' . implode(', ', $changedFields) : 'No explicit changes';

        $latestRevision = $post->revisions()->max('revision_number') ?? 0;
        PostRevision::create([
            'post_id' => $post->id,
            'author_id' => $request->user()->id,
            'title' => $post->getOriginal('title'),
            'slug' => $post->getOriginal('slug'),
            'excerpt' => $post->getOriginal('excerpt'),
            'content' => $post->getOriginal('content'),
            'revision_number' => $latestRevision + 1,
            'change_summary' => $changeSummary,
            'is_autosave' => false,
        ]);

        // Delete any autosaves since we are performing a manual save
        $post->revisions()->where('is_autosave', true)->delete();

        $post->save();

        $post->content = $this->processContentMedia($post, $post->content);
        $post->save();

        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags', []));
        }

        if ($request->has('categories') || $request->has('primary_category_id')) {
            $categories = $request->input('categories', []);
            if ($post->primary_category_id && !in_array($post->primary_category_id, $categories)) {
                $categories[] = $post->primary_category_id;
            }
            $post->categories()->sync($categories);
        }

        if ($request->hasFile('thumbnail')) {
            $post->clearMediaCollection('thumbnail');
            $post->addMediaFromRequest('thumbnail')
                ->toMediaCollection('thumbnail');
        } elseif ($request->filled('thumbnail_media_id')) {
            $media = \Modules\Core\Models\Media::find($request->thumbnail_media_id);
            if ($media) {
                $post->clearMediaCollection('thumbnail');
                $media->copy($post, 'thumbnail');
            }
        }

        $redirectUrl = session()->pull('posts_index_url', route('dashboard.posts.index'));
        
        return redirect($redirectUrl)->with('success', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();

        return back()->with('success', 'Post moved to trash.');
    }

    public function restore($id): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        Gate::authorize('delete', $post);
        $post->restore();

        return back()->with('success', 'Post restored.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        Gate::authorize('delete', $post);
        $post->forceDelete();

        return back()->with('success', 'Post permanently deleted.');
    }

    public function emptyTrash(): RedirectResponse
    {
        Gate::authorize('delete', new Post);
        $trashedPosts = Post::onlyTrashed()->get();
        foreach ($trashedPosts as $post) {
            $post->forceDelete();
        }

        return back()->with('success', 'Trash emptied successfully.');
    }

    public function revisions(Post $post): Response
    {
        Gate::authorize('update', $post);

        return Inertia::render('Posts/Revisions', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
            ],
            'revisions' => $post->revisions()
                ->with('author')
                ->orderByDesc('revision_number')
                ->get()
                ->map(fn ($rev) => [
                    'id' => $rev->id,
                    'revision_number' => $rev->revision_number,
                    'title' => $rev->title,
                    'excerpt' => $rev->excerpt,
                    'content' => $rev->content,
                    'change_summary' => $rev->change_summary,
                    'author' => $rev->author?->only('name'),
                    'created_at' => $rev->created_at,
                ]),
        ]);
    }

    public function restoreRevision(Post $post, $revisionId): RedirectResponse
    {
        Gate::authorize('update', $post);

        $revision = $post->revisions()->findOrFail($revisionId);

        $post->update([
            'title' => $revision->title,
            'excerpt' => $revision->excerpt,
            'content' => $revision->content,
        ]);

        return back()->with('success', 'Post restored to revision #' . $revision->revision_number);
    }

    public function autosave(Request $request, Post $post): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('update', $post);
        $autosave = $post->revisions()->firstOrNew(
            ['is_autosave' => true],
            [
                'author_id'       => $request->user()->id,
                'slug'            => $post->slug,
                'revision_number' => ($post->revisions()->max('revision_number') ?? 0) + 1,
                'change_summary'  => 'Autosave',
            ]
        );

        $autosave->fill([
            'title'   => $request->input('title', $post->title),
            'excerpt' => $request->input('excerpt', $post->excerpt),
            'content' => $request->input('content', $post->content),
        ]);

        $autosave->save();

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
            'data'    => [
                'updated_at' => $autosave->updated_at->toIso8601String(),
            ]
        ]);
    }

    private function processContentMedia(Post $post, ?string $html): string
    {
        if (empty($html)) {
            return (string) $html;
        }

        $trackedMediaIds = [];
        
        $html = preg_replace_callback('/<(?:img|a)[^>]+(?:src|href)=["\']([^"\']+)["\'][^>]*>/i', function ($matches) use ($post, &$trackedMediaIds) {
            $fullTag = $matches[0];
            $url = $matches[1];
            $mediaId = null;
            
            // Priority 1: data-media-id attribute
            if (preg_match('/data-media-id=["\'](\d+)["\']/i', $fullTag, $idMatches)) {
                $mediaId = (int) $idMatches[1];
            } else {
                // Priority 2: Extract from /storage/ path
                $path = parse_url($url, PHP_URL_PATH);
                if ($path && str_starts_with($path, '/storage/')) {
                    $segments = explode('/', trim($path, '/'));
                    // Check if it's a conversion path (e.g. 15/conversions/thumb.jpg)
                    if (count($segments) >= 3 && $segments[count($segments) - 2] === 'conversions') {
                        $mediaIdSegment = $segments[count($segments) - 3] ?? null;
                    } else if (count($segments) >= 2) {
                        $mediaIdSegment = $segments[count($segments) - 2] ?? null;
                    }

                    if (isset($mediaIdSegment) && is_numeric($mediaIdSegment)) {
                        $mediaId = (int) $mediaIdSegment;
                    }
                }
            }
            
            if ($mediaId) {
                /** @var \Modules\Core\Models\Media|null $media */
                $media = \Modules\Core\Models\Media::find($mediaId);
                
                if ($media) {
                    if ($media->model_type === Post::class && $media->model_id === $post->id && $media->collection_name === 'content_images') {
                        $finalMedia = $media;
                    } else {
                        $finalMedia = $media->copy($post, 'content_images');
                    }
                    
                    $trackedMediaIds[] = $finalMedia->id;
                    
                    if (stripos($fullTag, '<img') === 0) {
                        $thumbUrl = null;
                        try {
                            $thumbUrl = parse_url($finalMedia->getUrl('thumb'), PHP_URL_PATH);
                        } catch (\Exception $e) {
                            $thumbUrl = parse_url($finalMedia->getUrl(), PHP_URL_PATH);
                        }
                        $fullUrl = parse_url($finalMedia->getUrl(), PHP_URL_PATH);
                        
                        // Strip old src, data-src, class, data-media-id to prevent duplication
                        $cleanTag = preg_replace('/ (?:src|data-src|class|data-media-id)=["\'][^"\']*["\']/i', '', $fullTag);
                        $cleanTag = rtrim($cleanTag, '/> ') . '>';
                        
                        return str_replace(
                            '<img',
                            sprintf('<img src="%s" data-src="%s" data-media-id="%d" class="lazyload blur-up"', $thumbUrl, $fullUrl, $finalMedia->id),
                            $cleanTag
                        );
                    }
                    
                    $newPath = parse_url($finalMedia->getUrl(), PHP_URL_PATH);
                    return str_replace($url, $newPath, $fullTag);
                }
            }
            
            return $fullTag;
        }, $html);

        $existingMedia = $post->getMedia('content_images');
        foreach ($existingMedia as $media) {
            if (!in_array($media->id, $trackedMediaIds)) {
                $media->delete();
            }
        }

        return $html;
    }
}
