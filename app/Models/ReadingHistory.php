<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'post_id',
        'identity_key',
        'user_id',
        'first_read_at',
        'last_read_at',
        'read_count',
    ];

    protected $casts = [
        'first_read_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
