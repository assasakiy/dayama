<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'system_email_templates';

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];
}
