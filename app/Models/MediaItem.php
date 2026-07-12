<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class MediaItem extends BaseMedia
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function getIsVideoAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'video/');
    }

    public function getIsAudioAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'audio/');
    }

    public function getIsDocumentAttribute(): bool
    {
        $mime = $this->mime_type ?? '';

        return ! str_starts_with($mime, 'image/')
            && ! str_starts_with($mime, 'video/')
            && ! str_starts_with($mime, 'audio/');
    }
}