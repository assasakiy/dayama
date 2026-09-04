<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\StoreUserRequest;
use App\Http\Requests\Dashboard\UpdateUserRequest;
use App\Services\RoleAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Models\Institution;
use Modules\Core\Models\User;
use Spatie\Permission\Models\Role;

class UserController
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);
        $query = User::with('roles')
            ->withCount(['posts', 'comments'])
            ->with('roleUser.institution')
            ->latest();

        // Search
        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('email', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhereHas('person', fn ($pq) => $pq->where('nama_lengkap', 'like', "%{$search}%"))
            );
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by email verified
        if ($request->input('verified') === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->input('verified') === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $users = $query->paginate(15)->withQueryString()->through(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'status' => $user->status ?? 'active',
            'email_verified_at' => $user->email_verified_at,
            'last_login_at' => $user->last_login_at,
            'posts_count' => (int) $user->posts_count,
            'comments_count' => (int) $user->comments_count,
            'created_at' => $user->created_at,
            'roles' => $user->roles->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'color' => $r->color,
                'icon' => $r->icon,
            ]),
            'is_primary_super_admin' => $user->is_primary_super_admin,
            'is_protected' => $user->is_protected,
            'is_verified' => $user->is_verified,
            'highest_rank' => $user->getHighestRank(),
            'institution' => $user->roleUser->first()?->institution ? [
                'id' => $user->roleUser->first()->institution->id,
                'name' => $user->roleUser->first()->institution->name,
            ] : null,
            'can' => [
                'update' => request()->user()->can('update', $user),
                'delete' => request()->user()->can('delete', $user),
            ],
        ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(['id', 'name', 'color', 'icon', 'scope']),
            'filters' => $request->only(['search', 'role', 'status', 'verified']),
            'institutions' => Institution::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(User $user): Response
    {
        Gate::authorize('view', $user);
        $user->loadCount(['posts', 'comments'])->load('roles', 'roleUser.institution');

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'avatar_url' => $user->avatar_url,
                'biography' => $user->biography,
                'website' => $user->website,
                'social_links' => $user->social_links,
                'status' => $user->status ?? 'active',
                'posts_count' => $user->posts_count,
                'comments_count' => $user->comments_count,
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
                'email_verified_at' => $user->email_verified_at,
                'is_verified' => $user->is_verified,
                'updated_at' => $user->updated_at,
                'roles' => $user->roles->map(fn ($r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'color' => $r->color,
                ]),
                'institution' => $user->roleUser->first()?->institution ? [
                    'id' => $user->roleUser->first()->institution->id,
                    'name' => $user->roleUser->first()->institution->name,
                ] : null,
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'] ?? str($validated['email'])->before('@'),
            'password' => $validated['password'],
            'status' => $validated['status'] ?? 'active',
        ]);

        $profileData = [
            'full_name' => $validated['name'],
            'biography' => $validated['biography'] ?? null,
            'website' => $validated['website'] ?? null,
            'social_links' => array_filter([
                'github' => $validated['social_links']['github'] ?? null,
                'twitter' => $validated['social_links']['twitter'] ?? null,
                'linkedin' => $validated['social_links']['linkedin'] ?? null,
            ]),
        ];

        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->profile()->create($profileData);

        if (! empty($validated['roles']) || isset($validated['assignments'])) {
            app(RoleAssignmentService::class)->sync($user, $this->assignments($validated));
        }

        return redirect()->route('dashboard.users.index')->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        Gate::authorize('update', $user);
        $user->load(['roles', 'profile', 'roleUser.institution']);

        return Inertia::render('Users/Form', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'avatar_url' => $user->avatar_url,
                'biography' => $user->profile?->biography,
                'website' => $user->profile?->website,
                'social_links' => $user->profile?->social_links,
                'status' => $user->status ?? 'active',
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'roles' => $user->roles->pluck('name'),
                'is_protected' => $user->is_protected,
                'is_verified' => $user->is_verified,
                'institution' => $user->roleUser->first()?->institution ? [
                    'id' => $user->roleUser->first()->institution->id,
                    'name' => $user->roleUser->first()->institution->name,
                ] : null,
            ],
            'roles' => Role::orderBy('name')->get(['id', 'name', 'color', 'icon', 'scope']),
            'institutions' => Institution::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'email' => $validated['email'],
            'status' => $validated['status'] ?? 'active',
        ];

        if (auth()->user()->is_primary_super_admin) {
            if (isset($validated['is_protected'])) {
                $data['is_protected'] = $validated['is_protected'];
            }
            if (isset($validated['is_verified'])) {
                $data['is_verified'] = $validated['is_verified'];
            }
        } elseif (auth()->user()->getHighestRank() >= 100) {
            if (isset($validated['is_verified'])) {
                $data['is_verified'] = $validated['is_verified'];
            }
            if (isset($validated['is_protected']) && ! $user->is_primary_super_admin) {
                $data['is_protected'] = $validated['is_protected'];
            }
        }

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        $profileData = [
            'full_name' => $validated['name'],
            'biography' => $validated['biography'] ?? null,
            'website' => $validated['website'] ?? null,
            'social_links' => array_filter([
                'github' => $validated['social_links']['github'] ?? null,
                'twitter' => $validated['social_links']['twitter'] ?? null,
                'linkedin' => $validated['social_links']['linkedin'] ?? null,
            ]),
        ];

        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        if (isset($validated['roles']) || isset($validated['assignments'])) {
            app(RoleAssignmentService::class)->sync($user, $this->assignments($validated));
        }

        return redirect()->route('dashboard.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);
        $user->delete();

        return redirect()->route('dashboard.users.index')->with('success', 'User deleted.');
    }

    private function assignments(array $validated): array
    {
        if (isset($validated['assignments'])) {
            return $validated['assignments'];
        }

        return collect($validated['roles'] ?? [])->map(fn (string $role): array => [
            'role' => $role,
            'institution_id' => $validated['institution_id'] ?? null,
        ])->all();
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        Gate::authorize('delete', User::class);
        $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['uuid']]);

        User::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('dashboard.users.index')->with('success', 'Selected users deleted.');
    }

    public function bulkAssignRole(Request $request): RedirectResponse
    {
        Gate::authorize('update', User::class);
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['uuid'],
            'role' => ['required', 'string', 'exists:core_roles,name'],
            'institution_id' => ['nullable', 'uuid', 'exists:core_institutions,id'],
        ]);

        app(RoleAssignmentService::class)->bulkAssign(
            User::whereIn('id', $request->input('ids'))->get(),
            $request->input('role'),
            $request->input('institution_id'),
        );

        return redirect()->route('dashboard.users.index')->with('success', 'Role assigned to selected users.');
    }
}
