<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\AuthorizationService;
use Illuminate\Auth\Access\Response;
use Modules\Academic\Models\Student;
use Modules\Core\Models\User;

class StudentPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Student::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Student $student): Response
    {
        $result = $this->authService->check($user, 'view', $student);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Student::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Student $student): Response
    {
        $result = $this->authService->check($user, 'update', $student);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Student $student): Response
    {
        $result = $this->authService->check($user, 'delete', $student);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
