<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SystemAsset extends Model implements HasMedia
{
    use HasUuid, InteractsWithMedia;

    protected $table = 'system_assets';
    protected $guarded = [];

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(400)
             ->height(400)
             ->nonQueued();
    }
}
