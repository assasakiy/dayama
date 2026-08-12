<?php

declare(strict_types=1);

namespace Modules\AI\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'ai_conversations';

    protected $fillable = [
        'agent_id',
        'user_id',
        'session_id',
        'title',
        'metadata',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
