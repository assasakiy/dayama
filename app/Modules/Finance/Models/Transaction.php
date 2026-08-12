<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'finance_transactions';

    protected $fillable = [
        'from_account',
        'to_account',
        'amount',
        'type',
        'category',
        'description',
        'reference_id',
        'reference_type',
        'transaction_date',
        'created_by',
    ];
}
