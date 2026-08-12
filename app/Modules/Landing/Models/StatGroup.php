<?php

namespace Modules\Landing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StatGroup extends Model
{
    use HasUuids;

    protected $table = 'landing_stat_groups';

    protected $fillable = [
        'name',
        'items',
        'is_active',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];
}
