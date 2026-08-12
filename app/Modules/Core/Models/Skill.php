<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasUuids;

    protected $table = 'core_skills';

    protected $fillable = ['nama', 'slug', 'kategori'];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'core_person_skills')
            ->withPivot('level')
            ->withTimestamps();
    }
}
