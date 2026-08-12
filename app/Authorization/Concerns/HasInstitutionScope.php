<?php

namespace App\Authorization\Concerns;

use App\Authorization\Scopes\InstitutionScope;

trait HasInstitutionScope
{
    public static function bootHasInstitutionScope(): void
    {
        static::addGlobalScope(new InstitutionScope);
    }
}
