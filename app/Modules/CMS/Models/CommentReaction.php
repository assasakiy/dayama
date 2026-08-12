<?php

declare(strict_types=1);

namespace Modules\CMS\Models;

use App\Models\Concerns\HasUuid;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([\App\Observers\CommentReactionObserver::class])]
class CommentReaction extends Model
{
    use HasUuid;

    protected $table = 'cms_comment_reactions';

    protected $guarded = [];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
