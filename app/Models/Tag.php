<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasUserstamps;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes, HasUuid, HasSlug, HasUserstamps, LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted', 'restored', 'forceDeleted'];

    protected $guarded = [];

    protected $casts = [
        'is_visible' => 'boolean',
        'posts_count' => 'integer',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
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
