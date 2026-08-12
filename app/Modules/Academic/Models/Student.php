<?php

namespace Modules\Academic\Models;

use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasUuids;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'academic_students';

    protected $fillable = [
        'person_id', 'institution_id', 'nis', 'nisn', 'angkatan',
        'status', 'nama_ibu_kandung', 'tempat_tinggal', 'nomor_kk', 'nomor_kip',
        'cita_cita', 'hobi', 'foto', 'waktu_tempuh_menit', 'is_locked',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }
}
