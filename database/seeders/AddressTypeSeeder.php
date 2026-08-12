<?php

namespace Database\Seeders;

use Modules\Core\Models\AddressType;
use Illuminate\Database\Seeder;

class AddressTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Rumah', 'Domisili', 'Kantor', 'Pesantren / Asrama'];

        foreach ($types as $nama) {
            AddressType::firstOrCreate(['nama' => $nama]);
        }
    }
}
