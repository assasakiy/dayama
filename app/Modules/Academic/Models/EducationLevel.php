<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends Model
{
    use HasUuids;

    protected $table = 'academic_education_levels';

    protected $fillable = ['nama', 'urutan'];

    public function personEducations(): HasMany
    {
        return $this->hasMany(PersonEducation::class);
    }
}
