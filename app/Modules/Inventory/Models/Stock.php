<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_stocks';

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'recorded_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
