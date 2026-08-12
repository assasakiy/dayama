<?php

declare(strict_types=1);

namespace Modules\Landing\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'landing_pages';

    protected $guarded = [];

    protected $casts = [
        'sections'  => 'array',
        'is_active' => 'boolean',
    ];

    public function getSection(string $key, mixed $default = []): mixed
    {
        return data_get($this->sections, $key, $default);
    }

    public function setSection(string $key, mixed $value): void
    {
        $sections = $this->sections ?? [];
        data_set($sections, $key, $value);
        $this->sections = $sections;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    public function cta()
    {
        return $this->belongsTo(Cta::class);
    }

    public function statGroup()
    {
        return $this->belongsTo(StatGroup::class);
    }
}
