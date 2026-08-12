<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionAddress extends Model
{
    use HasUuids;

    protected $table = 'core_institution_addresses';

    protected $fillable = [
        'institution_id', 'alamat_jalan', 'rt', 'rw', 'kode_pos',
        'provinsi', 'kabupaten_kota', 'kecamatan', 'desa_kelurahan',
        'latitude', 'longitude',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
