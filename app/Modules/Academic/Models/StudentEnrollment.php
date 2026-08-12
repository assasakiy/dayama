<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEnrollment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_student_enrollments';

    protected $fillable = [
        'student_id', 'academic_year_id', 'semester_id', 'class_id', 'status_id',
        'entry_date', 'exit_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'exit_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(AClass::class, 'class_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StudentStatus::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function graduation(): HasOne
    {
        return $this->hasOne(Graduation::class);
    }
}
