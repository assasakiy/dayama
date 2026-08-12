<?php

namespace Database\Seeders;

use Modules\CRM\Models\RelationshipType;
use Illuminate\Database\Seeder;

class RelationshipTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Ayah', 'Ibu', 'Anak', 'Wali',
            'Kakak', 'Adik', 'Paman', 'Bibi',
            'Kakek', 'Nenek', 'Suami', 'Istri',
            'Menantu', 'Mertua', 'Saudara Kandung',
        ];

        foreach ($types as $nama) {
            RelationshipType::firstOrCreate(['nama' => $nama]);
        }
    }
}
