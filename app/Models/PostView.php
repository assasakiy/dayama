<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory, HasUuid;

    public const CREATED_AT = 'viewed_at';
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'viewed_at' => 'datetime',
        'dwell_time'=> 'integer',
        'scroll_depth'=> 'integer',
        'is_unique' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
