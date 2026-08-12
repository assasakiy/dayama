<?php

declare(strict_types=1);

namespace Modules\HR\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasUuid;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'hr_employees';

    protected $fillable = [
        'person_id',
        'institution_id',
        'employment_status_id',
        'department_id',
        'nuptk',
        'nip',
        'sudah_sertifikasi',
        'nomor_sertifikat_pendidik',
        'jam_mengajar_per_minggu',
    ];

    protected function casts(): array
    {
        return [
            'sudah_sertifikasi' => 'boolean',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Person::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Institution::class);
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
