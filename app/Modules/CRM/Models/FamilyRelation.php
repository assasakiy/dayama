<?php

namespace Modules\CRM\Models;

use Modules\Core\Models\Person;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyRelation extends Model
{
    use HasUuids;

    protected $table = 'crm_family_relations';

    protected $fillable = ['person_id', 'related_person_id', 'relationship_type_id'];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function relatedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'related_person_id');
    }

    public function relationshipType(): BelongsTo
    {
        return $this->belongsTo(RelationshipType::class);
    }
}
