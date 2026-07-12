<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Models\Post;
use App\Models\SystemAsset;
use App\Models\Role;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Media::class);

        $user = $request->user();
        
        $query = Media::query()->with('model')
            ->where(function($q) use ($user) {
                // If user is super admin, maybe they can see all, but let's stick to uploaded_by/is_public/owned
                $q->where('custom_properties->uploaded_by', $user->id)
                  ->orWhere('custom_properties->is_public', true)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('model_type', User::class)
                           ->where('model_id', $user->id);
                  })
                  ->orWhere(function($subQ) {
                      $subQ->where('model_type', Post::class)
                           ->whereHasMorph('model', [Post::class]);
                  })
                  ->orWhere('model_type', \App\Models\Category::class)
                  ->orWhere('model_type', SystemAsset::class);
            })
            ->latest();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type') && $request->input('type') !== 'all') {
            $type = $request->input('type');
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'document') {
                $query->where('mime_type', 'not like', 'image/%')
                      ->where('mime_type', 'not like', 'video/%');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            }
        }

        // Role filter
        if ($request->filled('role') && $request->input('role') !== 'all') {
            $roleName = Role::find($request->input('role'))->name ?? null;
            if ($roleName) {
                $userIdsByRole = User::role($roleName)->pluck('id');
                $query->whereIn('custom_properties->uploaded_by', $userIdsByRole);
            }
        }

        // User filter
        if ($request->filled('user') && $request->input('user') !== 'all') {
            $query->where('custom_properties->uploaded_by', $request->input('user'));
        }

        $media = $query->paginate(24);

        // Fetch uploaders for the current page
        $userIds = collect($media->items())->pluck('custom_properties.uploaded_by')->filter()->unique();
        $uploaders = User::whereIn('id', $userIds)->get()->keyBy('id');

        $media->through(fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'file_name' => $item->file_name,
            'mime_type' => $item->mime_type,
            'size' => $item->size,
            'human_readable_size' => $item->human_readable_size,
            'thumbnail_url' => (function() use ($item) {
                try {
                    return parse_url($item->getUrl('thumb'), PHP_URL_PATH);
                } catch (\Exception $e) {
                    return parse_url($item->getUrl(), PHP_URL_PATH);
                }
            })(),
            'original_url' => parse_url($item->getUrl(), PHP_URL_PATH),
            'created_at' => $item->created_at->toIso8601String(),
            'model_type' => $item->model_type,
            'model_id' => $item->model_id,
            'attached_to' => $item->model_type === Post::class && $item->model 
                ? 'Post: ' . $item->model->title 
                : ($item->model_type === SystemAsset::class ? 'Global Library' : class_basename($item->model_type)),
            'uploader' => isset($item->custom_properties['uploaded_by']) && isset($uploaders[$item->custom_properties['uploaded_by']])
                ? ['name' => $uploaders[$item->custom_properties['uploaded_by']]->name] 
                : ($item->model_type === Post::class && $item->model && $item->model->author ? ['name' => $item->model->author->name] : null),
            'custom_properties' => $item->custom_properties,
        ]);

        $filterRoles = Role::where('name', '!=', 'Subscriber')->get(['id', 'display_name', 'name']);
        
        $allUploaderIds = Media::whereNotNull('custom_properties->uploaded_by')
                                ->get(['custom_properties'])
                                ->pluck('custom_properties.uploaded_by')
                                ->unique()
                                ->filter()
                                ->values();
        $filterUsers = User::whereIn('id', $allUploaderIds)->get(['id', 'name']);

        return Inertia::render('Media/Index', [
            'media' => $media,
            'filters' => $request->only('search', 'type', 'role', 'user'),
            'filterRoles' => $filterRoles,
            'filterUsers' => $filterUsers
        ]);
    }

    public function apiIndex(Request $request): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('viewAny', Media::class);

        $user = $request->user();

        $query = Media::query()->with('model')
            ->where(function($q) use ($user) {
                $q->where('custom_properties->uploaded_by', $user->id)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('model_type', User::class)
                           ->where('model_id', $user->id);
                  })
                  ->orWhere('model_type', Post::class)
                  ->orWhere('model_type', \App\Models\Category::class)
                  ->orWhere('model_type', SystemAsset::class);
            })
            ->latest();
        
        $media = $query->paginate(30);
        
        $items = $media->getCollection()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'file_name' => $item->file_name,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'human_readable_size' => $item->human_readable_size,
                'thumbnail_url' => (function() use ($item) {
                    try {
                        return parse_url($item->getUrl('thumb'), PHP_URL_PATH);
                    } catch (\Exception $e) {
                        return parse_url($item->getUrl(), PHP_URL_PATH);
                    }
                })(),
                'original_url' => parse_url($item->getUrl(), PHP_URL_PATH),
                'created_at' => $item->created_at->toIso8601String(),
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'custom_properties' => $item->custom_properties,
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Media::class);

        $request->validate([
            'file' => ['required', 'image', 'max:5120'], // Max 5MB
        ]);

        $request->user()->addMedia($request->file('file'))
            ->withCustomProperties([
                'uploaded_by' => $request->user()->id,
                'is_public' => false
            ])
            ->toMediaCollection('library');

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $medium);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $medium->name = $request->input('name');
        $medium->file_name = \Illuminate\Support\Str::slug($request->input('name')) . '.' . pathinfo($medium->file_name, PATHINFO_EXTENSION);
        
        if ($request->has('is_public')) {
            $medium->setCustomProperty('is_public', $request->boolean('is_public'));
        }

        $medium->save();

        return redirect()->back()->with('success', 'Media renamed successfully.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $medium);

        $user = request()->user();
        $capabilities = app(\App\Authorization\AuthorizationService::class)->capabilities($user, Media::class);

        // 1. Jika punya hak aksi untuk semua (Permanent Delete)
        if ($capabilities->hasAll('delete')) {
            $medium->delete();
            return redirect()->back()->with('success', 'File deleted permanently.');
        }

        // 2. Jika hanya hak aksi pribadi (Adopsi Aset / Soft Remove)
        $medium->setCustomProperty('uploaded_by', 1);
        $medium->save();
        return redirect()->back()->with('success', 'Media removed from your collection (transferred to Admin).');
    }
}
