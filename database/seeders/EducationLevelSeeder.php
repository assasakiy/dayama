<?php

namespace Database\Seeders;

use Modules\Academic\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['nama' => 'PAUD / TK / RA', 'urutan' => 1],
            ['nama' => 'SD / MI',         'urutan' => 2],
            ['nama' => 'SMP / MTs',       'urutan' => 3],
            ['nama' => 'SMA / MA / SMK',  'urutan' => 4],
            ['nama' => 'D1',              'urutan' => 5],
            ['nama' => 'D2',              'urutan' => 6],
            ['nama' => 'D3',              'urutan' => 7],
            ['nama' => 'D4 / S1',         'urutan' => 8],
            ['nama' => 'S2 / Magister',   'urutan' => 9],
            ['nama' => 'S3 / Doktor',     'urutan' => 10],
        ];

        foreach ($levels as $data) {
            EducationLevel::firstOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
