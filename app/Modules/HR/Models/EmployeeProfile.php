<?php

namespace Modules\HR\Models;

use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
    use HasUuids;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'hr_employee_profiles';

    protected $fillable = [
        'person_id', 'institution_id', 'employment_status_id',
        'nuptk', 'nip', 'sudah_sertifikasi',
        'nomor_sertifikat_pendidik', 'jam_mengajar_per_minggu',
    ];

    protected function casts(): array
    {
        return ['sudah_sertifikasi' => 'boolean'];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
