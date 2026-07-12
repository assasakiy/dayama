<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ownership Mapping
    |--------------------------------------------------------------------------
    |
    | Map models to their respective ownership columns. You can map directly
    | to a database column name (string) or use a Closure to resolve deep
    | relationships (e.g. fn($model) => $model->relation->owner_id).
    |
    */
    App\Models\Post::class => 'author_id',
    App\Models\Comment::class => 'user_id',
    App\Models\ActivityLog::class => 'causer_id',
];
