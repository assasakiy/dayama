<?php

declare(strict_types=1);

namespace Modules\CMS\Models;

use App\Models\Concerns\HasUuid;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'post_id',
    'author_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'change_summary',
    'revision_number',
    'is_autosave',
    'metadata',
])]
class PostRevision extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $table = 'cms_post_revisions';

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_autosave' => 'boolean',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
