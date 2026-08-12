<?php

declare(strict_types=1);

namespace Modules\HR\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasUuid;

    protected $table = 'hr_attendances';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'recorded_by',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'recorded_by');
    }
}
