<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academic\Models\EducationLevel;

class PersonEducation extends Model
{
    use HasUuids;

    protected $table = 'core_person_educations';

    protected $fillable = [
        'person_id', 'education_level_id', 'institution_name',
        'jurusan', 'tahun_masuk', 'tahun_lulus', 'status',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }
}
