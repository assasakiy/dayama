<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ActiveInstitution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstitutionScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->is_primary_super_admin) {
            return $next($request);
        }

        $institution = $request->route('institution');

        if ($institution instanceof \Modules\Core\Models\Institution) {
            $institutionId = $institution->id;
        } elseif (is_string($institution)) {
            $institutionId = $institution;
        } else {
            $institutionId = $request->route('institution_id');
        }

        if (! $institutionId) {
            abort(403, 'Institution scope required.');
        }

        // Check yayasan scope — user can access all institutions
        $hasYayasanScope = $user->roles()->where('scope', 'yayasan')->exists();
        if ($hasYayasanScope) {
            return $next($request);
        }

        // Check lembaga scope via pivot
        $hasScope = \Modules\Core\Models\RoleUser::where('user_id', $user->id)
            ->where('institution_id', $institutionId)
            ->exists();

        if (! $hasScope) {
            abort(403, 'Anda tidak memiliki akses ke lembaga ini.');
        }

        return $next($request);
    }
}
