<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Models\FamilyRelation;

class RelationshipType extends Model
{
    use HasUuids;

    protected $table = 'core_relationship_types';

    protected $fillable = ['nama'];

    public function familyRelations(): HasMany
    {
        return $this->hasMany(FamilyRelation::class);
    }
}
