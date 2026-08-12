<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'crm_guardians';

    protected $fillable = [
        'person_id',
        'student_id',
        'relationship_type_id',
        'is_primary',
        'is_emergency_contact',
        'notes',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Person::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Academic\Models\Student::class);
    }

    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\RelationshipType::class);
    }
}
