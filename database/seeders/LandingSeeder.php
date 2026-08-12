<?php

declare(strict_types=1);

namespace Database\Seeders;

use Modules\Landing\Models\Cta;
use Modules\Landing\Models\Faq;
use Modules\Landing\Models\Page;
use Illuminate\Database\Seeder;

class LandingSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Pages ────────────────────────────────────────────────────────────
        Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'name'       => 'Home',
                'is_active'  => true,
                'sort_order' => 1,
                'sections'   => [
                    'hero' => [
                        'title'        => 'Pondok Pesantren',
                        'highlight'    => 'Darul Yatama Wal Masakin',
                        'subtitle'     => 'Mencetak generasi Qurani yang berakhlak mulia, berwawasan luas, dan siap menjadi pemimpin umat. Lembaga pendidikan Islam modern yang menggabungkan kurikulum nasional dengan pendidikan pesantren.',
                        'button1_text' => 'Program Pendidikan',
                        'button1_url'  => null,
                        'button2_text' => 'Pendaftaran Santri Baru',
                        'button2_url'  => null,
                    ],
                    'stats' => [
                        'items' => [
                            ['number' => '500+', 'label' => 'Santri Aktif'],
                            ['number' => '50+', 'label' => 'Tenaga Pengajar'],
                            ['number' => '20+', 'label' => 'Tahun Berdiri'],
                            ['number' => '1000+', 'label' => 'Alumni'],
                        ]
                    ],
                    'about' => [
                        'badge'       => 'Tentang Kami',
                        'heading'     => 'Mengenal Lebih Dekat<br>Pondok Pesantren Dayama',
                        'description1'=> 'Pondok Pesantren Darul Yatama Wal Masakin (Dayama) adalah lembaga pendidikan Islam yang berdedikasi untuk memberikan pendidikan berkualitas kepada anak-anak yatim, dhuafa, dan masyarakat umum. Dengan memadukan kurikulum nasional dan kurikulum pesantren, Dayama berupaya mencetak generasi yang tidak hanya unggul secara akademis, tetapi juga kokoh dalam iman dan akhlak.',
                        'description2'=> 'Didirikan dengan semangat keikhlasan dan pengabdian, Dayama terus berkembang menjadi lembaga pendidikan yang dipercaya masyarakat luas.',
                        'button1_text'=> 'Selengkapnya',
                        'button1_url' => null,
                        'button2_text'=> 'Visi & Misi',
                        'button2_url' => null,
                        'image'       => null,
                    ],
                    'features' => [
                        'badge'       => 'Keunggulan',
                        'heading'     => 'Mengapa Memilih Dayama?',
                        'subtitle'    => 'Program pendidikan terpadu yang mempersiapkan santri untuk kebahagiaan dunia dan akhirat.',
                        'items'       => [
                            ['icon' => 'BookOpen', 'title' => 'Kurikulum Terpadu', 'description' => 'Menggabungkan kurikulum nasional (Kemendikbud) dengan kurikulum pesantren dan Kemenag secara harmonis.'],
                            ['icon' => 'Star', 'title' => 'Program Tahfidz Al-Quran', 'description' => 'Program hafalan Al-Quran dengan metode yang teruji, dibimbing oleh para hafidz dan hafidzah berpengalaman.'],
                            ['icon' => 'Globe', 'title' => 'Bahasa Arab & Inggris', 'description' => 'Penguasaan bahasa internasional melalui program intensif percakapan harian dan laboratorium bahasa.'],
                            ['icon' => 'Home', 'title' => 'Fasilitas Lengkap', 'description' => 'Asrama nyaman, masjid, laboratorium, perpustakaan, lapangan olahraga, dan ruang multimedia modern.'],
                            ['icon' => 'Heart', 'title' => 'Pembinaan Karakter', 'description' => 'Pendidikan akhlak melalui suri tauladan, kedisiplinan, dan kegiatan keagamaan rutin yang membentuk kepribadian santri.'],
                            ['icon' => 'Gift', 'title' => 'Beasiswa Yatim & Dhuafa', 'description' => 'Membuka akses pendidikan berkualitas bagi anak-anak yatim dan keluarga kurang mampu melalui program beasiswa penuh.'],
                        ],
                    ],
                    'programs' => [
                        'badge'       => 'Program Kami',
                        'heading'     => 'Jenjang Pendidikan',
                        'subtitle'    => 'Beragam jenjang pendidikan yang tersedia untuk membentuk generasi yang berilmu dan berakhlak.',
                        'items'       => [
                            ['icon' => 'Book', 'title' => 'Pondok Pesantren', 'description' => 'Pendidikan kepesantrenan dengan asrama terpadu, kajian kitab kuning, dan pembinaan akhlak 24 jam.', 'url' => null],
                            ['icon' => 'GraduationCap', 'title' => 'Madrasah', 'description' => 'Pendidikan formal dari tingkat Ibtidaiyah hingga Aliyah dengan kurikulum Kemenag yang terstandarisasi.', 'url' => null],
                            ['icon' => 'BookOpen', 'title' => 'TPQ (Taman Pendidikan Quran)', 'description' => 'Pembelajaran Al-Quran untuk anak usia dini dengan metode yang menyenangkan dan mudah dipahami.', 'url' => null],
                        ],
                    ],
                ],
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'pendidikan'],
            [
                'name'       => 'Pendidikan',
                'is_active'  => true,
                'sort_order' => 2,
                'sections'   => [
                    'hero' => [
                        'title'     => 'Program Pendidikan',
                        'highlight' => 'Unggulan Kami',
                        'subtitle'  => 'Jenjang pendidikan berkualitas dari tingkat dasar hingga menengah atas.',
                        'image'     => null,
                    ],
                    'programs' => [
                        'heading'     => 'Jenjang Pendidikan',
                        'description' => 'Kami menyediakan berbagai jenjang pendidikan yang terakreditasi.',
                        'items'       => [],
                    ],
                ],
            ]
        );

        $this->command->info('✅ Pages seeded: Home, Pendidikan');

        Page::updateOrCreate(
            ['slug' => 'profil'],
            [
                'name'       => 'Profil',
                'is_active'  => true,
                'sort_order' => 3,
                'sections'   => [
                    'hero' => [
                        'title'     => 'Profil',
                        'highlight' => 'Pesantren',
                        'subtitle'  => 'Mengenal lebih dekat sejarah, visi, dan misi pesantren kami.',
                        'image'     => null,
                    ],
                    'about' => [
                        'heading'     => 'Sejarah Berdiri',
                        'description' => 'Pesantren ini berdiri sejak puluhan tahun lalu dengan niat tulus untuk mendidik umat.',
                    ]
                ],
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'layanan'],
            [
                'name'       => 'Layanan',
                'is_active'  => true,
                'sort_order' => 4,
                'sections'   => [
                    'hero' => [
                        'title'     => 'Layanan',
                        'highlight' => 'Masyarakat',
                        'subtitle'  => 'Berbagai layanan yang kami sediakan untuk umat.',
                        'image'     => null,
                    ],
                    'features' => [
                        'heading' => 'Layanan Kami',
                        'items' => []
                    ]
                ],
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'media'],
            [
                'name'       => 'Media',
                'is_active'  => true,
                'sort_order' => 5,
                'sections'   => [
                    'hero' => [
                        'title'     => 'Galeri',
                        'highlight' => 'Media',
                        'subtitle'  => 'Dokumentasi kegiatan dan acara di pesantren kami.',
                        'image'     => null,
                    ],
                ],
            ]
        );

        // --- SUB PAGES ---
        
        // 1. PROFIL
        Page::updateOrCreate(['slug' => 'tentang-yayasan'], [
            'name' => 'Tentang Yayasan', 'is_active' => true, 'sort_order' => 6,
            'sections' => [
                'hero' => ['title' => 'Tentang', 'highlight' => 'Yayasan', 'subtitle' => 'Mengenal lebih dekat Yayasan Pondok Pesantren Darul Yatama Wal Masakin.', 'image' => null],
                'about' => ['heading' => 'Yayasan Dayama', 'description' => 'Yayasan ini menaungi berbagai lembaga pendidikan dari tingkat dasar hingga menengah.'],
            ]
        ]);
        
        Page::updateOrCreate(['slug' => 'sejarah'], [
            'name' => 'Sejarah', 'is_active' => true, 'sort_order' => 7,
            'sections' => [
                'hero' => ['title' => 'Sejarah', 'highlight' => 'Berdiri', 'subtitle' => 'Perjalanan panjang lembaga pendidikan kami dari masa ke masa.', 'image' => null],
                'about' => ['heading' => 'Awal Mula Berdiri', 'description' => 'Pesantren ini berdiri berkat gotong royong masyarakat.'],
            ]
        ]);
        
        Page::updateOrCreate(['slug' => 'visi-misi'], [
            'name' => 'Visi & Misi', 'is_active' => true, 'sort_order' => 8,
            'sections' => [
                'hero' => ['title' => 'Visi &', 'highlight' => 'Misi', 'subtitle' => 'Tujuan utama dan langkah nyata kami dalam membangun generasi Qurani.', 'image' => null],
                'features' => ['heading' => 'Visi Kami', 'items' => []],
            ]
        ]);

        Page::updateOrCreate(['slug' => 'mitra'], [
            'name' => 'Mitra & Kerjasama', 'is_active' => true, 'sort_order' => 9,
            'sections' => [
                'hero' => ['title' => 'Mitra', 'highlight' => 'Kerjasama', 'subtitle' => 'Jaringan kerja sama dengan berbagai instansi dan lembaga.', 'image' => null],
            ]
        ]);

        // 2. LAYANAN
        Page::updateOrCreate(['slug' => 'psb'], [
            'name' => 'Pendaftaran Santri Baru (PSB)', 'is_active' => true, 'sort_order' => 10,
            'sections' => [
                'hero' => ['title' => 'Pendaftaran', 'highlight' => 'Santri Baru', 'subtitle' => 'Informasi lengkap mengenai prosedur pendaftaran santri baru.', 'image' => null],
            ]
        ]);

        Page::updateOrCreate(['slug' => 'donasi'], [
            'name' => 'Donasi', 'is_active' => true, 'sort_order' => 11,
            'sections' => [
                'hero' => ['title' => 'Mari', 'highlight' => 'Berdonasi', 'subtitle' => 'Bantu kami dalam mengembangkan pendidikan bagi anak-anak yatim dan dhuafa.', 'image' => null],
            ]
        ]);

        Page::updateOrCreate(['slug' => 'faq'], [
            'name' => 'Tanya Jawab (FAQ)', 'is_active' => true, 'sort_order' => 12,
            'faq_category' => 'umum',
            'sections' => [
                'hero' => ['title' => 'Tanya', 'highlight' => 'Jawab', 'subtitle' => 'Pertanyaan yang sering diajukan mengenai pesantren kami.', 'image' => null],
            ]
        ]);

        Page::updateOrCreate(['slug' => 'unit-bisnis'], [
            'name' => 'Unit Bisnis', 'is_active' => true, 'sort_order' => 13,
            'sections' => [
                'hero' => ['title' => 'Unit', 'highlight' => 'Bisnis', 'subtitle' => 'Usaha mandiri pesantren untuk mendukung biaya operasional dan pendidikan santri.', 'image' => null],
            ]
        ]);

        $this->command->info('✅ Sub Pages seeded: Tentang, Sejarah, Visi Misi, Mitra, PSB, Donasi, FAQ, Unit Bisnis');

        // ─── FAQ ──────────────────────────────────────────────────────────────
        $faqs = [
            ['question' => 'Bagaimana cara mendaftar?', 'answer' => 'Pendaftaran dapat dilakukan secara online melalui halaman PSB atau datang langsung ke sekretariat pondok.', 'sort_order' => 1],
            ['question' => 'Berapa biaya pendidikan?', 'answer' => 'Informasi biaya pendidikan dapat dilihat pada brosur resmi atau menghubungi bagian administrasi.', 'sort_order' => 2],
            ['question' => 'Apakah tersedia beasiswa?', 'answer' => 'Ya, kami menyediakan beasiswa bagi santri berprestasi dan kurang mampu.', 'sort_order' => 3],
            ['question' => 'Apa saja fasilitas yang tersedia?', 'answer' => 'Pondok kami dilengkapi asrama, masjid, laboratorium, perpustakaan, dan lapangan olahraga.', 'sort_order' => 4],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }

        $this->command->info('✅ FAQs seeded: ' . count($faqs) . ' entries');

        // ─── CTAs ─────────────────────────────────────────────────────────────
        $ctas = [
            [
                'name'        => 'CTA Pendaftaran',
                'title'       => 'Siap Bergabung?',
                'subtitle'    => 'Daftarkan putra-putri Anda sekarang dan jadilah bagian dari keluarga besar pondok pesantren modern.',
                'button_text' => 'Daftar Sekarang',
                'button_url'  => null, // fallback ke PSB
            ],
            [
                'name'        => 'CTA Donasi',
                'title'       => 'Mari Berpartisipasi',
                'subtitle'    => 'Setiap kontribusi Anda sangat berarti bagi kemajuan pendidikan santri.',
                'button_text' => 'Donasi Sekarang',
                'button_url'  => null, // fallback ke halaman donasi
            ],
        ];

        foreach ($ctas as $cta) {
            Cta::updateOrCreate(
                ['name' => $cta['name']],
                array_merge($cta, ['is_active' => true])
            );
        }

        $this->command->info('✅ CTAs seeded: ' . count($ctas) . ' entries');
    }
}
