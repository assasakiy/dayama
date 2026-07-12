<?php

namespace App\Support;

use App\Models\Post;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $modelType = $media->model_type;
        $collectionName = $media->collection_name ?: 'default';
        $mediaId = $media->getKey();
        
        $modelId = $media->model_id;
        
        if ($modelType === User::class && $modelId) {
            // e.g. users/1/avatars/1
            return "users/{$modelId}/{$collectionName}/{$mediaId}";
        }
        
        if ($modelType === Post::class && $modelId) {
            // e.g. posts/2/2
            return "posts/{$modelId}/{$mediaId}";
        }
        
        if ($modelType === \App\Models\SystemAsset::class) {
            return "systemassets/{$collectionName}/{$mediaId}";
        }
        
        if ($modelType === \App\Models\Category::class) {
            return "categories/{$mediaId}";
        }
        
        // Default fallback (e.g. App\Models\Role -> roles/default/3)
        $classBasename = strtolower(class_basename($modelType));
        return "{$classBasename}s/{$collectionName}/{$mediaId}";
    }
}
