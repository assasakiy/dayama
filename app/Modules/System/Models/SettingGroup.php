<?php

declare(strict_types=1);

namespace Modules\System\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'name',
    'description',
    'icon',
    'sort_order',
])]
class SettingGroup extends Model
{
    use HasUuid;

    protected $table = 'system_setting_groups';
}
