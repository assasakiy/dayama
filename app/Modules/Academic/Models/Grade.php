<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_grades';

    protected $fillable = [
        'student_enrollment_id', 'subject_id', 'semester_id',
        'score', 'grade_letter', 'notes', 'graded_by',
    ];

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
