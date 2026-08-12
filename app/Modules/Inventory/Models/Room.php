<?php

declare(strict_types=1);

namespace Modules\Inventory\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_rooms';

    protected $fillable = [
        'name',
        'code',
        'location',
        'capacity',
        'description',
        'is_active',
    ];
}
