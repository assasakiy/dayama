<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressType extends Model
{
    use HasUuids;

    protected $table = 'core_address_types';

    protected $fillable = ['nama'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
