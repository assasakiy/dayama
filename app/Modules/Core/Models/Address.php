<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasUuids;

    protected $table = 'core_addresses';

    protected $fillable = [
        'person_id', 'address_type_id', 'alamat', 'provinsi',
        'kabupaten_kota', 'kecamatan', 'desa_kelurahan', 'kode_pos',
        'latitude', 'longitude', 'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AddressType::class, 'address_type_id');
    }
}
