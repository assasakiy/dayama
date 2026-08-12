<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'finance_donations';

    protected $fillable = [
        'donor_id',
        'amount',
        'donation_date',
        'payment_type_id',
        'campaign',
        'is_anonymous',
        'notes',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(\Modules\CRM\Models\Donor::class);
    }
}
