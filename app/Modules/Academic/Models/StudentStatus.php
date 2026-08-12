<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentStatus extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_student_statuses';

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
