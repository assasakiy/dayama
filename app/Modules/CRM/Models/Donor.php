<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'crm_donors';

    protected $fillable = [
        'person_id',
        'donor_type',
        'is_anonymous',
        'notes',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Person::class);
    }
}
