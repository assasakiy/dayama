<?php

declare(strict_types=1);

namespace Modules\AI\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prompt extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'ai_prompts';

    protected $fillable = [
        'agent_id',
        'title',
        'content',
        'category',
        'variables',
        'is_active',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
