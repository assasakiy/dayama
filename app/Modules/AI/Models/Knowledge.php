<?php

declare(strict_types=1);

namespace Modules\AI\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowledge extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'ai_knowledge';

    protected $fillable = [
        'title',
        'content',
        'source_type',
        'source_id',
        'tags',
        'is_active',
    ];
}
