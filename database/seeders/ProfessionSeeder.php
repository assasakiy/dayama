<?php

namespace Database\Seeders;

use Modules\Core\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $professions = [
            'Guru / Ustadz', 'Dosen', 'ASN / PNS', 'PPPK',
            'TNI / Polri', 'Dokter', 'Perawat', 'Bidan',
            'Petani', 'Pedagang / Wirausaha', 'Programmer / IT',
            'Akuntan / Keuangan', 'Arsitek', 'Pengacara',
            'Mahasiswa', 'Pelajar', 'Ibu Rumah Tangga',
            'Belum Bekerja', 'Lainnya',
        ];

        foreach ($professions as $nama) {
            Profession::firstOrCreate(['nama' => $nama]);
        }
    }
}
