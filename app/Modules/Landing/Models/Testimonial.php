<?php

declare(strict_types=1);

namespace Modules\Landing\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'landing_testimonials';

    protected $fillable = [
        'name',
        'title',
        'avatar',
        'content',
        'rating',
        'is_active',
        'sort_order',
    ];
}
