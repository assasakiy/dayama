<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasUuids;

    protected $table = 'core_certificates';

    protected $fillable = [
        'person_id', 'nama', 'penerbit', 'nomor',
        'tanggal_terbit', 'expired_at', 'file',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'expired_at' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
