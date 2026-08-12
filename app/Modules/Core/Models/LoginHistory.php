<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'ip_address',
    'user_agent',
    'device',
    'platform',
    'browser',
    'location',
    'is_successful',
    'failure_reason',
    'logged_in_at',
    'logged_out_at',
])]
class LoginHistory extends Model
{
    use HasUuid;

    protected $table = 'core_login_histories';

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
            'logged_out_at' => 'datetime',
            'is_successful' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('is_successful', true);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('is_successful', false);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('logged_in_at', 'desc');
    }
}
