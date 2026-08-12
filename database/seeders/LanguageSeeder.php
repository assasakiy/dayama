<?php

namespace Database\Seeders;

use Modules\Core\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            'Bahasa Indonesia', 'Bahasa Arab', 'Bahasa Inggris',
            'Bahasa Jawa', 'Bahasa Sunda', 'Bahasa Madura',
            'Bahasa Sasak', 'Bahasa Minang', 'Bahasa Bugis',
        ];

        foreach ($languages as $nama) {
            Language::firstOrCreate(['nama' => $nama]);
        }
    }
}
