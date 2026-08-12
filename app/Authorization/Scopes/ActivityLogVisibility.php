<?php

namespace App\Authorization\Scopes;

use App\Authorization\Contracts\VisibilityScope;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogVisibility implements VisibilityScope
{
    public function apply(Builder $query, User $actor): Builder
    {
        if ($actor->is_primary_super_admin) {
            return $query;
        }

        $seeAll = $actor->can('activity_logs.view.all');

        if (!$seeAll) {
            return $query->where('causer_type', User::class)->where('causer_id', $actor->id);
        }

        // Apply rank scope for regular users (cannot see logs of higher or equal ranked users)
        return $query->where(function ($q) use ($actor) {
            $myRank = $actor->getHighestRank();
            $higherRankUserIds = User::whereHas('roles', function ($roleQuery) use ($myRank) {
                $roleQuery->where('rank', '>=', $myRank);
            })->where('id', '!=', $actor->id)->pluck('id');

            if ($higherRankUserIds->isNotEmpty()) {
                $q->where(function ($subQ) use ($higherRankUserIds) {
                    $subQ->where(function ($sub) use ($higherRankUserIds) {
                        $sub->where('causer_type', User::class)
                            ->whereNotIn('causer_id', $higherRankUserIds);
                    })
                    ->orWhere('causer_type', '!=', User::class)
                    ->orWhereNull('causer_type');
                });
            }
        });
    }
}
