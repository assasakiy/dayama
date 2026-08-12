<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactType extends Model
{
    use HasUuids;

    protected $table = 'core_contact_types';

    protected $fillable = ['nama', 'icon'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
