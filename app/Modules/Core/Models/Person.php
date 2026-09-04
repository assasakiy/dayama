<?php

namespace Modules\Core\Models;

use Modules\Academic\Models\PersonEducation;
use Modules\Academic\Models\Student;
use Modules\Academic\Models\TeachingAssignment;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmploymentHistory;
use Modules\HR\Models\Position;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use \App\Models\Concerns\HasUuid, SoftDeletes;

    protected $table = 'core_persons';

    protected $fillable = [
        'nik', 'passport',
        'nama_lengkap',
        'gelar_depan', 'gelar_belakang',
        'gender', 'tempat_lahir', 'tanggal_lahir',
        'agama', 'status_hidup', 'photo',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'status_hidup'  => 'boolean',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(InstitutionMembership::class, 'person_id');
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'core_institution_memberships')
            ->withPivot(['id', 'status', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function activeInstitutions(): BelongsToMany
    {
        return $this->institutions()->wherePivot('status', 'active');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'person_id');
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'core_person_positions')
            ->withPivot(['institution_id', 'nomor_induk', 'tanggal_mulai', 'tanggal_selesai', 'status'])
            ->withTimestamps();
    }

    public function activePositions(): BelongsToMany
    {
        return $this->positions()->wherePivot('status', 'aktif');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(PersonEducation::class);
    }

    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(Profession::class, 'core_person_professions')
            ->withPivot(['is_primary', 'mulai', 'selesai'])
            ->withTimestamps();
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function familyMembers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'crm_family_relations', 'person_id', 'related_person_id')
            ->withPivot('relationship_type_id')
            ->withTimestamps();
    }

    public function familyOf(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'crm_family_relations', 'related_person_id', 'person_id')
            ->withPivot('relationship_type_id')
            ->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'core_person_skills')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'core_person_languages')->withTimestamps();
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
