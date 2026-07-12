<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\IdentityMigrationService;

class MigrateGuestDataToUser
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected IdentityMigrationService $migrationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $visitorToken = request()->cookie('visitor_token');
        
        if (! $visitorToken) {
            return;
        }

        $userId = $event->user->getAuthIdentifier();
        
        $this->migrationService->mergeGuestIdentity($visitorToken, $userId);
    }
}
