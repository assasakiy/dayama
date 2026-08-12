<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authorization Pipeline Rules
    |--------------------------------------------------------------------------
    |
    | This array defines the rules that will be executed in the authorization
    | pipeline. The order is STRICT and matters greatly. The provider will
    | validate this order during boot to fail fast if it's incorrect.
    |
    */
    'rules' => [
        App\Authorization\Rules\PrimarySuperAdminRule::class,
        App\Authorization\Rules\ScopeRule::class,
        App\Authorization\Rules\PermissionRule::class,
        App\Authorization\Rules\OwnershipRule::class,
        App\Authorization\Rules\RankRule::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Visibility Scopes
    |--------------------------------------------------------------------------
    |
    | Define the mapping between eloquent models and their corresponding
    | visibility scopes.
    |
    */
    'visibility' => [
        Modules\System\Models\ActivityLog::class => App\Authorization\Scopes\ActivityLogVisibility::class,
        Modules\Core\Models\User::class => App\Authorization\Scopes\UserVisibility::class,
        Modules\Core\Models\Role::class => App\Authorization\Scopes\RoleVisibility::class,
    ],
];
