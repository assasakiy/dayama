<?php

namespace Modules\Yayasan\Models;

use Modules\Core\Models\Institution;
use Modules\Core\Models\Person;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonTransferLog extends Model
{
    use HasUuids;

    protected $table = 'person_transfer_logs';

    protected $fillable = [
        'from_institution_id',
        'to_institution_id',
        'source_person_id',
        'destination_person_id',
        'nik',
        'triggered_by',
    ];

    public function fromInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'from_institution_id');
    }

    public function toInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'to_institution_id');
    }

    public function sourcePerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'source_person_id');
    }

    public function destinationPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'destination_person_id');
    }

    public function trigger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
