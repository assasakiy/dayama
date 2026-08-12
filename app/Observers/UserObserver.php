<?php

namespace App\Observers;

use Modules\Core\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        $superAdmin = User::where('is_primary_super_admin', true)->first();
        
        if ($superAdmin && $superAdmin->id !== $user->id) {
            $mediaItems = $user->media()->where('collection_name', 'library')->get();
            
            foreach ($mediaItems as $media) {
                $customProperties = $media->custom_properties;
                $customProperties['original_uploader_id'] = $user->id;
                $customProperties['original_uploader_name'] = $user->name;
                $media->custom_properties = $customProperties;
                $media->save();
                
                $oldUrl = parse_url($media->getUrl(), PHP_URL_PATH);
                
                $media->move($superAdmin, 'library');
                
                $newUrl = parse_url($media->getUrl(), PHP_URL_PATH);
                
                if ($oldUrl && $newUrl && $oldUrl !== $newUrl) {
                    \Modules\CMS\Models\Post::where('content', 'LIKE', '%' . $oldUrl . '%')
                        ->each(function (\Modules\CMS\Models\Post $post) use ($oldUrl, $newUrl) {
                            $post->content = str_replace($oldUrl, $newUrl, $post->content);
                            $post->save();
                        }, 100);
                }
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
