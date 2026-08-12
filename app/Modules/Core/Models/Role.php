<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $table = 'core_roles';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'slug',
        'color',
        'icon',
        'is_system',
        'status',
        'scope',
        'sort_order',
        'rank',
    ];

    public const SCOPES = ['yayasan', 'lembaga'];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'sort_order' => 'integer',
            'rank' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system;
    }

    public function isDeletable(): bool
    {
        return ! $this->isSystem();
    }
}
