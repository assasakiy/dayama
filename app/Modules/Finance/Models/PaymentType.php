<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'finance_payment_types';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];
}
