<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasUuids;

    protected $table = 'academic_academic_years';

    protected $fillable = ['nama', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
