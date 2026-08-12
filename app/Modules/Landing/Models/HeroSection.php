<?php

declare(strict_types=1);

namespace Modules\Landing\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroSection extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'landing_hero_sections';

    protected $fillable = [
        'page_id',
        'title',
        'subtitle',
        'description',
        'background_image',
        'background_color',
        'cta_text',
        'cta_url',
        'is_active',
        'sort_order',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
