<?php

namespace Database\Seeders;

use Modules\Core\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['nama' => 'Kepala Madrasah / Pimpinan Pondok', 'sort_order' => 0],
            ['nama' => 'Wakil Kepala / Mudir', 'sort_order' => 1],
            ['nama' => 'Guru / Ustadz', 'sort_order' => 2],
            ['nama' => 'Guru Piket', 'sort_order' => 3],
            ['nama' => 'Staf Tata Usaha', 'sort_order' => 4],
            ['nama' => 'Staf Keuangan / Bendahara', 'sort_order' => 5],
            ['nama' => 'Pustakawan', 'sort_order' => 6],
            ['nama' => 'Musyrif / Musyrifah (Pendamping Asrama)', 'sort_order' => 7],
            ['nama' => 'Santri / Siswa', 'sort_order' => 8],
            ['nama' => 'Alumni', 'sort_order' => 9],
            ['nama' => 'Wali Santri / Orang Tua', 'sort_order' => 10],
            ['nama' => 'Donatur', 'sort_order' => 11],
        ];

        foreach ($positions as $data) {
            Position::firstOrCreate(
                ['slug' => Str::slug($data['nama'])],
                [
                    'nama' => $data['nama'],
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}
