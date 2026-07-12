<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasUserstamps;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends \Illuminate\Database\Eloquent\Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasUuid, HasSlug, HasUserstamps, LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted', 'restored', 'forceDeleted'];

    protected $table = 'posts';

    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'meta_keywords' => 'array',
        'og_data' => 'array',
        'json_ld' => 'array',
        'slug' => 'string',
        'reactions_breakdown' => 'array',
    ];

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['views_count', 'reading_time', 'word_count', 'comments_count', 'shares_count', 'reactions_count', 'updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function mediaItems(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model');
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function readingHistories(): HasMany
    {
        return $this->hasMany(ReadingHistory::class);
    }

    // Accessors
    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('thumbnail');

        if ($media) {
            return parse_url($media->getUrl('thumb'), PHP_URL_PATH);
        }

        return null;
    }

    // Media conversions
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('small')
            ->width(800)
            ->sharpen(5);

        $this->addMediaConversion('large')
            ->width(1600)
            ->sharpen(3);

        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();
            
        $this->addMediaCollection('content_images');
    }

    // Sluggable definition for HasSlug trait
    public function sluggable(): array
    {
        return ['title'];
    }

    public static function syncCounts(): void
    {
        \Illuminate\Support\Facades\DB::statement('UPDATE tags SET posts_count = (SELECT COUNT(*) FROM post_tag WHERE post_tag.tag_id = tags.id)');
        \Illuminate\Support\Facades\DB::statement('UPDATE categories SET posts_count = (SELECT COUNT(*) FROM category_post WHERE category_post.category_id = categories.id)');
    }
}
