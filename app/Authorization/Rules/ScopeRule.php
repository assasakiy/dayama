<?php

namespace App\Authorization\Rules;

use App\Authorization\AuthorizationContext;
use App\Authorization\Contracts\AuthorizationRule;
use Closure;
use Modules\Core\Models\RoleUser;

class ScopeRule implements AuthorizationRule
{
    public function handle(AuthorizationContext $context, Closure $next): AuthorizationContext
    {
        $actor = $context->actor;

        // Only enforce for users with lembaga-scoped roles
        $hasLembagaScope = $actor->roles()->where('scope', 'lembaga')->exists();
        if (! $hasLembagaScope) {
            return $next($context);
        }

        $target = $context->target;

        if (! is_object($target)) {
            return $next($context);
        }

        $institutionId = $this->resolveInstitutionId($target);
        if (! $institutionId) {
            return $next($context);
        }

        $hasAccess = RoleUser::where('user_id', $actor->id)
            ->where('institution_id', $institutionId)
            ->exists();

        if (! $hasAccess) {
            $context->deny('Anda tidak memiliki akses ke sumber daya di lembaga ini.');
            return $context;
        }

        return $next($context);
    }

    private function resolveInstitutionId(object $target): ?string
    {
        if (isset($target->institution_id)) {
            return $target->institution_id;
        }

        if (method_exists($target, 'getAttribute') && $target->getAttribute('institution_id')) {
            return $target->getAttribute('institution_id');
        }

        return null;
    }
}
