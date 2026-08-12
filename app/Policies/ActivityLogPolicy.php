<?php

namespace App\Policies;

use App\Authorization\AuthorizationService;
use Modules\System\Models\ActivityLog;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityLogPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', ActivityLog::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, ActivityLog $log): Response
    {
        $result = $this->authService->check($user, 'delete', $log);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
