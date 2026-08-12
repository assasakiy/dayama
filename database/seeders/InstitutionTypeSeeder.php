<?php

namespace Database\Seeders;

use Modules\Core\Models\InstitutionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstitutionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nama' => 'Yayasan', 'sort_order' => 0],
            ['nama' => 'Pondok Pesantren', 'sort_order' => 1],
            ['nama' => 'Madrasah Ibtidaiyah (MI)', 'sort_order' => 2],
            ['nama' => 'Madrasah Tsanawiyah (MTs)', 'sort_order' => 3],
            ['nama' => 'Madrasah Aliyah (MA)', 'sort_order' => 4],
            ['nama' => 'Madrasah Aliyah Kejuruan (MAK)', 'sort_order' => 5],
            ['nama' => 'Raudhatul Athfal (RA)', 'sort_order' => 6],
            ['nama' => 'TPA / TPQ', 'sort_order' => 7],
            ['nama' => 'Lembaga Tahfidz', 'sort_order' => 8],
            ['nama' => 'PAUD / TK', 'sort_order' => 9],
            ['nama' => 'Sekolah Dasar (SD)', 'sort_order' => 10],
            ['nama' => 'Sekolah Menengah Pertama (SMP)', 'sort_order' => 11],
            ['nama' => 'Sekolah Menengah Atas (SMA)', 'sort_order' => 12],
            ['nama' => 'Sekolah Menengah Kejuruan (SMK)', 'sort_order' => 13],
        ];

        foreach ($types as $data) {
            InstitutionType::firstOrCreate(
                ['slug' => Str::slug($data['nama'])],
                [
                    'nama' => $data['nama'],
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}
