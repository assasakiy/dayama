<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'backupable_type',
    'backupable_id',
    'status',
    'filename',
    'size',
    'files',
    'metadata',
    'logs',
    'created_by',
])]
class Backup extends Model
{
    use HasUuid;

    protected $table = 'system_backups';

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'metadata' => 'array',
        ];
    }

    public function backupable(): MorphTo
    {
        return $this->morphTo('backupable');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
