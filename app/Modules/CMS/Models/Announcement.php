<?php

namespace Modules\CMS\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\User;

class Announcement extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'cms_announcements';

    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'published_at',
        'author_id', 'is_published', 'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
