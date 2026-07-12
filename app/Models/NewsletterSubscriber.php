<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscriber extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'preferences' => 'array',
        'subscribed_at'=> 'datetime',
        'unsubscribed_at'=> 'datetime',
        'verified_at'=> 'datetime',
    ];
}
