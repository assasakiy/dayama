<?php

declare(strict_types=1);

namespace Modules\Landing\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cta extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'landing_ctas';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getResolvedUrlAttribute(): string
    {
        return $this->button_url ?: url('/');
    }
}
