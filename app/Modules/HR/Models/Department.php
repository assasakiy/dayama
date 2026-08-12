<?php

declare(strict_types=1);

namespace Modules\HR\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;

class Department extends Model
{
    use HasUuid, SoftDeletes;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'hr_departments';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'institution_id',
        'parent_id',
        'kepala_person_id',
        'head_employee_id',
        'sort_order',
        'is_active',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function kepalaPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'kepala_person_id');
    }

    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }
}
