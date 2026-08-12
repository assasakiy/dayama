<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasUuids;

    protected $table = 'core_contacts';

    protected $fillable = ['person_id', 'contact_type_id', 'value', 'is_primary'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ContactType::class, 'contact_type_id');
    }
}
