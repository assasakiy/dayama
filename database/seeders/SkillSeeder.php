<?php

namespace Database\Seeders;

use Modules\Core\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Keagamaan
            ['nama' => "Tahfidz Al-Qur'an",        'kategori' => 'Keagamaan'],
            ['nama' => "Tilawah / Qiro'ah",         'kategori' => 'Keagamaan'],
            ['nama' => 'Bahasa Arab',                'kategori' => 'Keagamaan'],
            ['nama' => 'Kitab Kuning',               'kategori' => 'Keagamaan'],
            ['nama' => 'Kaligrafi',                  'kategori' => 'Keagamaan'],
            // Akademik
            ['nama' => 'Bahasa Indonesia',           'kategori' => 'Bahasa'],
            ['nama' => 'Bahasa Inggris',             'kategori' => 'Bahasa'],
            ['nama' => 'Matematika',                 'kategori' => 'Akademik'],
            ['nama' => 'IPA / Sains',                'kategori' => 'Akademik'],
            ['nama' => 'IPS',                        'kategori' => 'Akademik'],
            // Teknologi
            ['nama' => 'Komputer / MS Office',       'kategori' => 'Teknologi'],
            ['nama' => 'Pemrograman / Coding',       'kategori' => 'Teknologi'],
            ['nama' => 'Desain Grafis',              'kategori' => 'Teknologi'],
            // Lainnya
            ['nama' => 'Kepemimpinan',               'kategori' => 'Soft Skill'],
            ['nama' => 'Komunikasi Publik',          'kategori' => 'Soft Skill'],
            ['nama' => 'Akuntansi / Keuangan',       'kategori' => 'Profesional'],
        ];

        foreach ($skills as $data) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($data['nama'])],
                ['nama' => $data['nama'], 'kategori' => $data['kategori']]
            );
        }
    }
}
