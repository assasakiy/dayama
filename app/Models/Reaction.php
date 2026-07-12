<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'type' => 'string',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
