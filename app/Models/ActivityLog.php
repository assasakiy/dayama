<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    use HasUuid;

    public function post()
    {
        return $this->belongsTo(Post::class, 'subject_id')->where('subject_type', Post::class);
    }
}
