<?php

declare(strict_types=1);

namespace Modules\AI\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'ai_agents';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'model',
        'system_prompt',
        'temperature',
        'max_tokens',
        'is_active',
    ];
}
