<?php

namespace Modules\Core\Models;

use Modules\Academic\Models\Classroom;
use Modules\Academic\Models\Student;
use Modules\HR\Models\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core_institutions';

    protected $guarded = ['id'];

    protected $casts = [
        'facilities' => 'array',
        'extracurriculars' => 'array',
        'is_active' => 'boolean',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class, 'institution_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function legality(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InstitutionLegality::class);
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InstitutionAddress::class);
    }

    public function institutionContacts(): HasMany
    {
        return $this->hasMany(InstitutionContact::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
