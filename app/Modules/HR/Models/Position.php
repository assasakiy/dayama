<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model
{
    use HasUuids;

    protected $table = 'hr_positions';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'jenis_jabatan',
        'sort_order',
    ];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Core\Models\Person::class, 'core_person_positions')
            ->withPivot(['institution_id', 'nomor_induk', 'tanggal_mulai', 'tanggal_selesai', 'status'])
            ->withTimestamps();
    }

    public function activePersons(): BelongsToMany
    {
        return $this->persons()->wherePivot('status', 'aktif');
    }

}
