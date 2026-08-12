<?php

namespace Modules\Academic\Models;

use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingAssignment extends Model
{
    use HasUuids;

    protected $table = 'academic_teaching_assignments';

    protected $fillable = ['person_id', 'subject_id', 'classroom_id', 'jam_per_minggu'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
