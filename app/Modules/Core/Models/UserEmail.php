<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'email',
    'email_verified_at',
    'is_primary',
    'verification_code',
    'verification_code_expires_at',
    'verification_sent_at',
])]
class UserEmail extends Model
{
    use HasUuid;

    protected $table = 'core_user_emails';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_primary' => 'boolean',
            'verification_code_expires_at' => 'datetime',
            'verification_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
