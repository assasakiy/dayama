<?php

namespace App\Authorization\Rules;

use App\Authorization\AbilityResolver;
use App\Authorization\AuthorizationContext;
use App\Authorization\Contracts\AuthorizationRule;
use Closure;

class PermissionRule implements AuthorizationRule
{
    public function __construct(
        private AbilityResolver $abilityResolver
    ) {}

    public function handle(AuthorizationContext $context, Closure $next): AuthorizationContext
    {
        $resolution = $this->abilityResolver->resolve($context->ability, $context->target);

        $hasAnyPermission = false;

        foreach ($resolution->permissions() as $permission) {
            try {
                if ($context->actor->hasPermissionTo($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                // Skip jika permission varian (seperti .own/.all) belum ada di DB Spatie
                continue;
            }
        }

        if (!$hasAnyPermission) {
            $context->deny('You do not have the required permissions for this action.');
            return $context;
        }

        return $next($context);
    }
}
