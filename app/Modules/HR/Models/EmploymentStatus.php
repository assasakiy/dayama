<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentStatus extends Model
{
    use HasUuids;

    protected $table = 'hr_employment_statuses';

    protected $fillable = ['nama'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
