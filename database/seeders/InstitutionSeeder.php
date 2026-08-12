<?php

namespace Database\Seeders;

use Modules\Core\Models\InstitutionType;
use Illuminate\Database\Seeder;
use Modules\Core\Models\Institution;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $maType = InstitutionType::where('slug', 'madrasah-aliyah-ma')->first();
        $mtsType = InstitutionType::where('slug', 'madrasah-tsanawiyah-mts')->first();

        $institutions = [
            [
                'name' => 'Madrasah Aliyah (MA) Dayama',
                'slug' => 'ma',
                'institution_type_id' => $maType?->id,
                'status' => 'lengkap',
                'short_description' => 'Pendidikan tingkat menengah atas yang berfokus pada penguasaan ilmu agama dan sains modern.',
                'content' => '<p>Madrasah Aliyah (MA) Dayama menerapkan sistem pendidikan integratif yang menggabungkan keunggulan pesantren salaf (tradisional) dengan metode pembelajaran modern. Kurikulum yang diterapkan berpusat pada penguasaan literatur klasik Islam (Kitab Kuning) dipadukan dengan wawasan kebangsaan dan keterampilan abad 21.</p>',
                'facilities' => [
                    ['icon' => 'home', 'name' => 'Asrama Putra & Putri', 'description' => 'Terpisah dan representatif dengan pengawasan 24 jam.'],
                    ['icon' => 'book-open', 'name' => 'Perpustakaan', 'description' => 'Koleksi kitab klasik dan buku referensi modern.'],
                    ['icon' => 'monitor', 'name' => 'Laboratorium Komputer', 'description' => 'Fasilitas pembelajaran TIK bagi para santri.'],
                    ['icon' => 'video', 'name' => 'Multimedia & Studio', 'description' => 'Mendukung pengembangan bakat santri di bidang digital.']
                ],
                'extracurriculars' => [
                    "Tahfidzul Qur'an",
                    'Kajian Kitab Kuning',
                    "Seni Baca Al-Qur'an (Qiro'ah)",
                    'Pramuka & PMR',
                    'Kaligrafi & Jurnalistik'
                ],
                'registration_url' => '/layanan/psb',
                'sort_order' => 1,
            ],
            [
                'name' => 'Madrasah Tsanawiyah (MTs) Dayama',
                'slug' => 'mts',
                'institution_type_id' => $mtsType?->id,
                'status' => 'lengkap',
                'short_description' => 'Pendidikan tingkat menengah pertama berbasis karakter Islami.',
                'content' => '<p>Madrasah Tsanawiyah (MTs) Dayama mendidik santri dengan pondasi agama yang kuat, mengembangkan potensi akademik dan non-akademik sejak usia dini.</p>',
                'facilities' => [
                    ['icon' => 'home', 'name' => 'Asrama Khusus MTs', 'description' => 'Pendampingan penuh oleh musyrif/musyrifah.'],
                    ['icon' => 'book', 'name' => 'Ruang Kelas Nyaman', 'description' => 'Dilengkapi proyektor dan sistem sirkulasi udara yang baik.']
                ],
                'extracurriculars' => [
                    'Pramuka',
                    'Hadroh / Banjari',
                    'Pencak Silat',
                    'Pidato 3 Bahasa'
                ],
                'registration_url' => '/layanan/psb',
                'sort_order' => 2,
            ]
        ];

        foreach ($institutions as $data) {
            Institution::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
