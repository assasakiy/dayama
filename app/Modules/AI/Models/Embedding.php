<?php

declare(strict_types=1);

namespace Modules\AI\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    use HasUuid;

    protected $table = 'ai_embeddings';

    protected $fillable = [
        'embeddable_type',
        'embeddable_id',
        'content',
        'embedding',
        'model',
        'chunk_index',
        'metadata',
    ];
}
