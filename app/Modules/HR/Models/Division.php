<?php

declare(strict_types=1);

namespace Modules\HR\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'hr_divisions';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'department_id',
        'description',
        'head_employee_id',
        'sort_order',
        'is_active',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }
}
