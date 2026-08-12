<?php

namespace App\Authorization\Scopes;

use App\Support\ActiveInstitution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! ActiveInstitution::shouldScope()) {
            return;
        }

        $id = ActiveInstitution::id();
        if ($id) {
            $builder->where($model->getTable() . '.institution_id', $id);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}
