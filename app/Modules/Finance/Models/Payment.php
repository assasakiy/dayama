<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'finance_payments';

    protected $fillable = [
        'payment_type_id',
        'payable_type',
        'payable_id',
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'paid_by',
        'is_verified',
        'verified_by',
    ];

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
