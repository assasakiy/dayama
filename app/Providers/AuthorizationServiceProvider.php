<?php

namespace App\Providers;

use App\Authorization\AbilityResolver;
use App\Authorization\Resolvers\OwnershipResolver;
use App\Authorization\VisibilityManager;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Singletons
        $this->app->singleton(AbilityResolver::class);
        $this->app->singleton(OwnershipResolver::class);
        $this->app->singleton(VisibilityManager::class);
    }

    public function boot(): void
    {
        $this->validatePipelineRules();
        $this->registerVisibilityScopes();
    }

    private function validatePipelineRules(): void
    {
        $rules = config('authorization.rules', []);

        // Define the exact expected order
        $expectedOrder = [
            \App\Authorization\Rules\PrimarySuperAdminRule::class,
            \App\Authorization\Rules\PermissionRule::class,
            \App\Authorization\Rules\OwnershipRule::class,
            \App\Authorization\Rules\RankRule::class,
        ];

        // Fail fast if rules do not match the expected rigid structure
        if ($rules !== $expectedOrder) {
            throw new InvalidArgumentException(
                'Authorization Pipeline Rules are out of order. ' .
                'Strict order required: PrimarySuperAdminRule -> PermissionRule -> OwnershipRule -> RankRule.'
            );
        }
    }

    private function registerVisibilityScopes(): void
    {
        $visibilityManager = $this->app->make(VisibilityManager::class);
        $scopes = config('authorization.visibility', []);

        foreach ($scopes as $modelClass => $scopeClass) {
            $visibilityManager->register($modelClass, $scopeClass);
        }
    }
}
