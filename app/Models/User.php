<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'banner',
    'preferences',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',
    'last_login_at',
    'status',
    'is_primary_super_admin',
    'is_protected',
    'is_verified',
])]
#[Hidden([
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
])]
class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable, HasUuid, LogsActivity;

    /** @use HasFactory<UserFactory> */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'last_login_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function getHighestRank(): int
    {
        return $this->roles()->max('rank') ?? 0;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')->singleFile();
        $this->addMediaCollection('banners')->singleFile();
        $this->addMediaCollection('library');
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->nonQueued()
            ->performOnCollections('avatars', 'library', 'banners');
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(UserEmail::class, 'user_id');
    }

    public function connectedAccounts(): HasMany
    {
        return $this->hasMany(ConnectedAccount::class, 'user_id');
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->hasMedia('avatars')) {
            return parse_url($this->getFirstMediaUrl('avatars'), PHP_URL_PATH) ?? '';
        }

        // Fallback to legacy profile avatar
        if ($this->profile && $this->profile->avatar) {
            return parse_url(Storage::url($this->profile->avatar), PHP_URL_PATH);
        }

        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    public function getBannerUrlAttribute(): ?string
    {
        if ($this->hasMedia('banners')) {
            return parse_url($this->getFirstMediaUrl('banners'), PHP_URL_PATH);
        }
        
        // Fallback to legacy banner
        if ($this->banner) {
            return parse_url(Storage::url($this->banner), PHP_URL_PATH);
        }
        return null;
    }

    public function getAvatarMediaAttribute(): ?array
    {
        $media = $this->getFirstMedia('avatars');
        if (!$media) return null;

        return [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'human_readable_size' => $media->human_readable_size,
            'thumbnail_url' => parse_url($media->getUrl('thumb'), PHP_URL_PATH),
            'original_url' => parse_url($media->getUrl(), PHP_URL_PATH),
            'created_at' => $media->created_at->toISOString(),
            'model_type' => $media->model_type,
            'custom_properties' => $media->custom_properties,
        ];
    }

    public function getBannerMediaAttribute(): ?array
    {
        $media = $this->getFirstMedia('banners');
        if (!$media) return null;

        return [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'human_readable_size' => $media->human_readable_size,
            'thumbnail_url' => parse_url($media->getUrl('thumb'), PHP_URL_PATH),
            'original_url' => parse_url($media->getUrl(), PHP_URL_PATH),
            'created_at' => $media->created_at->toISOString(),
            'model_type' => $media->model_type,
            'custom_properties' => $media->custom_properties,
        ];
    }

    public function getBiographyAttribute(): ?string
    {
        return $this->profile?->biography;
    }

    public function getWebsiteAttribute(): ?string
    {
        return $this->profile?->website;
    }

    public function getSocialLinksAttribute(): ?array
    {
        return $this->profile?->social_links;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'is_primary_super_admin' => 'boolean',
            'is_protected' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }
}