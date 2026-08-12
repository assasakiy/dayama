<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_attendance';

    protected $fillable = [
        'student_enrollment_id', 'date', 'status', 'notes', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }
}
