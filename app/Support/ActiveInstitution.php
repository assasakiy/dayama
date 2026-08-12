<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\RoleUser;

class ActiveInstitution
{
    public static function id(): ?string
    {
        return session('active_institution_id');
    }

    public static function shouldScope(): bool
    {
        $user = request()?->user();
        if (! $user || $user->is_primary_super_admin) {
            return false;
        }

        return $user->roles()->where('scope', 'lembaga')->exists();
    }

    /**
     * Apply institution scope to a query.
     * For lembaga-scoped users, filters to their active institution.
     * For yayasan/super-admin, no filtering applied.
     */
    public static function applyToQuery(Builder $query, string $column = 'institution_id'): Builder
    {
        if (! self::shouldScope()) {
            return $query;
        }

        $id = self::id();
        if ($id) {
            return $query->where($column, $id);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Authorize access to a specific institution.
     * Throws 403 if the user does not have scope for the given institution.
     */
    public static function authorizeAccess(?string $institutionId): void
    {
        $user = request()?->user();
        if (! $user || $user->is_primary_super_admin) {
            return;
        }

        if (! $institutionId) {
            return;
        }

        $hasYayasanScope = $user->roles()->where('scope', 'yayasan')->exists();
        if ($hasYayasanScope) {
            return;
        }

        $hasAccess = RoleUser::where('user_id', $user->id)
            ->where('institution_id', $institutionId)
            ->exists();

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke lembaga ini.');
        }
    }

    /**
     * Get the accessible institution IDs for the current user.
     * Returns null for super-admin/yayasan (unrestricted), or an array of IDs for lembaga users.
     */
    public static function accessibleIds(): ?array
    {
        $user = request()?->user();
        if (! $user || $user->is_primary_super_admin) {
            return null;
        }

        $hasYayasanScope = $user->roles()->where('scope', 'yayasan')->exists();
        if ($hasYayasanScope) {
            return null;
        }

        $ids = RoleUser::where('user_id', $user->id)
            ->pluck('institution_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return ! empty($ids) ? $ids : null;
    }

    public static function scope(Builder $query, string $column = 'institution_id'): Builder
    {
        return self::applyToQuery($query, $column);
    }
}
