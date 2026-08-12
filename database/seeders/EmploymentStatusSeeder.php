<?php

namespace Database\Seeders;

use Modules\HR\Models\EmploymentStatus;
use Illuminate\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'PNS',
            'PPPK',
            'GTY (Guru Tetap Yayasan)',
            'GTT (Guru Tidak Tetap)',
            'Honorer',
            'Sukarela / Volunteer',
        ];

        foreach ($statuses as $nama) {
            EmploymentStatus::firstOrCreate(['nama' => $nama]);
        }
    }
}
