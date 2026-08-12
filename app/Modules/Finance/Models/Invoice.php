<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'finance_invoices';

    protected $fillable = [
        'invoice_number',
        'invoiceable_type',
        'invoiceable_id',
        'student_id',
        'amount',
        'due_date',
        'status',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\Modules\Academic\Models\Student::class);
    }
}
