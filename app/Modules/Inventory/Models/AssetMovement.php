<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMovement extends Model
{
    use HasUuid;

    protected $table = 'inventory_asset_movements';

    protected $fillable = [
        'item_id',
        'from_room_id',
        'to_room_id',
        'quantity',
        'movement_date',
        'reason',
        'notes',
        'recorded_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'to_room_id');
    }
}
