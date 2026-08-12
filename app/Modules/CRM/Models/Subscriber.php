<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'crm_subscribers';

    protected $fillable = [
        'email',
        'name',
        'phone',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];
}
