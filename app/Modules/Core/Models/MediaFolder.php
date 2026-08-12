<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFolder extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $table = 'core_media_folders';

    protected $guarded = [];

    protected $casts = [
        'files_count' => 'integer',
        'total_size'  => 'integer',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model');
    }
}
