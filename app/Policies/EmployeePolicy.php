<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\AuthorizationService;
use Illuminate\Auth\Access\Response;
use Modules\HR\Models\Employee;
use Modules\Core\Models\User;

class EmployeePolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Employee::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Employee $employee): Response
    {
        $result = $this->authService->check($user, 'view', $employee);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Employee::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Employee $employee): Response
    {
        $result = $this->authService->check($user, 'update', $employee);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Employee $employee): Response
    {
        $result = $this->authService->check($user, 'delete', $employee);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
