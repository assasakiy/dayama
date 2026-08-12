<?php

namespace Modules\Academic\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Graduation extends Model
{
    use HasUuid;

    protected $table = 'academic_graduations';

    protected $fillable = [
        'student_enrollment_id', 'graduation_date', 'certificate_number',
        'final_score', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'graduation_date' => 'date',
            'final_score' => 'float',
        ];
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }
}
