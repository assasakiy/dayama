<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionLegality extends Model
{
    use HasUuids;

    protected $table = 'core_institution_legalities';

    protected $fillable = [
        'institution_id', 'nspp', 'npsn', 'kode_registrasi',
        'nomor_ijop', 'tanggal_ijop', 'nomor_akta_yayasan',
        'npwp', 'tahun_berdiri_masehi', 'tahun_berdiri_hijriyah',
    ];

    protected function casts(): array
    {
        return ['tanggal_ijop' => 'date'];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
