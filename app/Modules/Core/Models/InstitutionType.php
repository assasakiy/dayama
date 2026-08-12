<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstitutionType extends Model
{
    use HasUuids;

    protected $table = 'core_institution_types';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'sort_order',
    ];

    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class, 'institution_type_id');
    }
}
