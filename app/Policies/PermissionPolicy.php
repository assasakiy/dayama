<?php

namespace App\Policies;

use Modules\Core\Models\Permission;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class PermissionPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Permission::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Permission $permission): Response
    {
        $result = $this->authService->check($user, 'view', $permission);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Permission::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Permission $permission): Response
    {
        $result = $this->authService->check($user, 'update', $permission);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Permission $permission): Response
    {
        $result = $this->authService->check($user, 'delete', $permission);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Permission $permission): Response
    {
        $result = $this->authService->check($user, 'restore', $permission);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Permission $permission): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $permission);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
