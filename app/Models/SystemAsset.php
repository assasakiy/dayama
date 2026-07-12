<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SystemAsset extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'system_assets';
    protected $guarded = [];

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(400)
             ->height(400)
             ->nonQueued(); // or keep it queued depending on your setup
    }
}
