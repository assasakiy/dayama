<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasUuids;

    protected $table = 'academic_subjects';

    protected $fillable = ['nama', 'kode'];

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'academic_teaching_assignments')
            ->withPivot(['person_id', 'jam_per_minggu'])
            ->withTimestamps();
    }
}
