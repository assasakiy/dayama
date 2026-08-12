<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Media extends BaseMedia
{
    use LogsActivity;

    protected $table = 'core_media';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
