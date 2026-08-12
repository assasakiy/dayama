<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_asset_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
    ];
}
