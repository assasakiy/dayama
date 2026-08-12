<?php

declare(strict_types=1);

namespace Modules\Landing\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'landing_partners';

    protected $fillable = [
        'name',
        'logo',
        'website',
        'is_active',
        'sort_order',
    ];
}
