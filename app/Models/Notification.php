<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'data'   => 'array',
        'read_at'=> 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
