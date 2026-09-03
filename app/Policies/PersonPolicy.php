<?php

declare(strict_types=1);

namespace App\Policies;

use App\Authorization\AuthorizationService;
use Illuminate\Auth\Access\Response;
use Modules\Core\Models\Person;
use Modules\Core\Models\User;

class PersonPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Person::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Person $person): Response
    {
        $result = $this->authService->check($user, 'view', $person);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Person::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Person $person): Response
    {
        $result = $this->authService->check($user, 'update', $person);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Person $person): Response
    {
        $result = $this->authService->check($user, 'delete', $person);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
