<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'crm_partners';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'contact_person',
        'email',
        'phone',
        'address',
        'website',
        'logo',
        'is_active',
    ];
}
