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

        // Resolusi khusus Person
        if ($target instanceof \Modules\Core\Models\Person) {
            $actorInstIds = RoleUser::where('user_id', $actor->id)
                ->pluck('institution_id')
                ->map(fn ($id) => (string) $id)
                ->filter()
                ->toArray();

            if ($activeInstId = \App\Support\ActiveInstitution::id()) {
                $actorInstIds[] = (string) $activeInstId;
            }

            $personInstIds = \Illuminate\Support\Facades\DB::table('core_institution_memberships')
                ->where('person_id', $target->id)
                ->pluck('institution_id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            $hasSharedMembership = !empty(array_intersect($actorInstIds, $personInstIds));

            if ($hasSharedMembership) {
                return $next($context);
            }

            $context->deny('Anda tidak memiliki akses ke data person di lembaga ini.');
            return $context;
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

        // Resolusi khusus Person: periksa apakah Person terhubung ke lembaga yang dapat diakses aktor
        if ($target instanceof \Modules\Core\Models\Person) {
            $accessibleInstIds = \App\Support\ActiveInstitution::accessibleIds() ?? [];
            if ($activeInstId = \App\Support\ActiveInstitution::id()) {
                $accessibleInstIds[] = $activeInstId;
            }
            
            // Cek apakah ada irisan membership target dengan lembaga yang dipegang aktor
            $sharedInstId = $target->memberships()->whereIn('institution_id', $accessibleInstIds)->value('institution_id');
            if ($sharedInstId) {
                return $sharedInstId;
            }
            
            // Jika tidak ada irisan sama sekali, kembalikan institusi target agar diblokir
            return $target->memberships()->value('institution_id') ?? '00000000-0000-0000-0000-000000000000';
        }

        return null;
    }
}
