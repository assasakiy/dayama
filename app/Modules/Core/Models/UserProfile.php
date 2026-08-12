<?php

namespace Modules\Core\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'core_user_profiles';

    protected $fillable = [
        'user_id',
        'full_name',
        'nickname',
        'avatar',
        'banner',
        'biography',
        'website',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
