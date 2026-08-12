<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Modules\CMS\Models\Post;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class ActivityLog extends SpatieActivity
{
    use HasUuid;

    protected $table = 'system_activity_logs';

    public function post()
    {
        return $this->belongsTo(Post::class, 'subject_id')->where('subject_type', Post::class);
    }
}
