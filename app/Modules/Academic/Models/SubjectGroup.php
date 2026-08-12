<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectGroup extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_subject_groups';

    protected $fillable = [
        'name', 'slug', 'description', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
