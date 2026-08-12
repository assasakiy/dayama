<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'provider_name',
    'provider_id',
    'access_token',
    'refresh_token',
    'expires_at',
])]
class ConnectedAccount extends Model
{
    use HasUuid;

    protected $table = 'core_connected_accounts';

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
