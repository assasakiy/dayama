<?php

namespace App\Policies;

use App\Models\PermissionGroup;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class PermissionGroupPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, PermissionGroup $permissionGroup): Response
    {
        $result = $this->authService->check($user, 'view', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, PermissionGroup $permissionGroup): Response
    {
        $result = $this->authService->check($user, 'update', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, PermissionGroup $permissionGroup): Response
    {
        $result = $this->authService->check($user, 'delete', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, PermissionGroup $permissionGroup): Response
    {
        $result = $this->authService->check($user, 'restore', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, PermissionGroup $permissionGroup): Response
    {
        $result = $this->authService->check($user, 'forceDelete', Role::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
