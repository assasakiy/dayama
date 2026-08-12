<?php

namespace Modules\HR\Models;

use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentHistory extends Model
{
    use HasUuids;
    use \App\Authorization\Concerns\HasInstitutionScope;

    protected $table = 'hr_employment_histories';

    protected $fillable = ['person_id', 'institution_id', 'jabatan', 'mulai', 'selesai'];

    protected function casts(): array
    {
        return [
            'mulai' => 'date',
            'selesai' => 'date',
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
}
