<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'room_id',
        'description',
        'quantity',
        'minimum_stock',
        'unit',
        'condition',
        'purchase_date',
        'purchase_price',
        'supplier',
        'image',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
