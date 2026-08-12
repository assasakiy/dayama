<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $table = 'core_permissions';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'guard_name',
        'module',
        'action',
        'scope',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }
        });
    }

    public function permissionGroups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class, 'core_permission_group_permission');
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->module && $this->action) {
            $parts = [$this->module, $this->action];
            if ($this->scope) {
                $parts[] = $this->scope;
            }
            return implode('.', $parts);
        }
        return $this->name;
    }
}
