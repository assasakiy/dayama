<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasUuids;

    protected $table = 'core_languages';

    protected $fillable = ['nama'];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'core_person_languages')->withTimestamps();
    }
}
