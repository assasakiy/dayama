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
    Modules\CMS\Models\Post::class => 'author_id',
    Modules\CMS\Models\Comment::class => 'user_id',
    Modules\System\Models\ActivityLog::class => 'causer_id',
];
