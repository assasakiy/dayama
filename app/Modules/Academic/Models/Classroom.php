<?php

namespace Modules\Academic\Models;

use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasUuids;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'academic_classrooms';

    protected $fillable = [
        'institution_id', 'academic_year_id', 'wali_kelas_person_id',
        'nama', 'tingkat',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'wali_kelas_person_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'academic_classroom_student')->withTimestamps();
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
