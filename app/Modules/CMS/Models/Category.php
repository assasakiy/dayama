<?php

declare(strict_types=1);

namespace Modules\CMS\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasUserstamps;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Core\Models\Media;

class Category extends \Illuminate\Database\Eloquent\Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasUuid, HasSlug, InteractsWithMedia, HasUserstamps, LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted', 'restored', 'forceDeleted'];

    protected $table = 'cms_categories';

    protected $guarded = [];

    protected $casts = [
        'is_visible' => 'boolean',
        'posts_count' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('image');
        if ($media) {
            return parse_url($media->getUrl(), PHP_URL_PATH);
        }
        return null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'cms_category_post');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['posts_count', 'updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function sluggable(): array
    {
        return ['name'];
    }
}
