<?php

namespace Modules\Yayasan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PersonIndex extends Model
{
    use HasUuids;

    protected $table = 'yayasan_person_index';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'tanggal_lahir',
        'refs',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'refs' => 'array',
        ];
    }
}
