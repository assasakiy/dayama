<?php

declare(strict_types=1);

namespace Database\Seeders;

use Modules\Core\Models\Setting;
use Modules\Core\Models\SettingGroup;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Setting Groups ───────────────────────────────────────────────────
        $groups = [
            ['key' => 'general',    'name' => 'Umum',              'icon' => 'Settings',       'sort_order' => 1,  'description' => 'Informasi dasar situs, identitas, SEO, dan tampilan.'],
            ['key' => 'media',      'name' => 'Media',             'icon' => 'Image',          'sort_order' => 2,  'description' => 'Pengaturan unggahan file dan perpustakaan media.'],
            ['key' => 'mail',       'name' => 'Email',             'icon' => 'Mail',           'sort_order' => 3,  'description' => 'Konfigurasi pengiriman email.'],
            ['key' => 'security',   'name' => 'Keamanan',          'icon' => 'Shield',         'sort_order' => 4,  'description' => 'Autentikasi, pembatasan akses, dan mode perbaikan.'],
        ];

        foreach ($groups as $group) {
            SettingGroup::updateOrCreate(['key' => $group['key']], $group);
        }

        // ─── Settings ─────────────────────────────────────────────────────────
        $settings = [

            // General
            ['group' => 'general', 'key' => 'general.site_name',         'value' => 'Blog Saya',         'type' => 'string',  'description' => 'Nama situs Anda.'],
            ['group' => 'general', 'key' => 'general.tagline',           'value' => 'Sebuah blog modern',   'type' => 'string',  'description' => 'Deskripsi singkat yang ditampilkan di bawah nama situs.'],
            ['group' => 'general', 'key' => 'general.site_description',  'value' => 'Selamat datang di blog modern kami tempat berbagi informasi terbaru.', 'type' => 'text', 'description' => 'Deskripsi detail situs untuk tampilan umum dan fallback SEO.'],
            ['group' => 'general', 'key' => 'general.logo_url',      'value' => null,              'type' => 'string',  'description' => 'URL gambar logo situs Anda.'],
            ['group' => 'general', 'key' => 'general.favicon_url',   'value' => null,              'type' => 'string',  'description' => 'URL favicon Anda (.ico atau .png).'],
            ['group' => 'general', 'key' => 'general.timezone',      'value' => 'Asia/Jakarta',    'type' => 'string',  'description' => 'Zona waktu default untuk tampilan tanggal.'],
            ['group' => 'general', 'key' => 'general.language',      'value' => 'id',              'type' => 'string',  'description' => 'Kode bahasa default situs (mis. id, en).'],
            ['group' => 'general', 'key' => 'general.date_format',   'value' => 'd M Y',           'type' => 'string',  'description' => 'Format tanggal PHP untuk menampilkan tanggal.'],

            // SEO (now part of general)
            ['group' => 'general', 'key' => 'seo.custom_seo_enabled',   'value' => false,             'type' => 'boolean', 'description' => 'Aktifkan konfigurasi SEO kustom. Jika dinonaktifkan, SEO dihasilkan secara otomatis.'],
            ['group' => 'general', 'key' => 'seo.meta_title_suffix',    'value' => '| Blog Saya',     'type' => 'string',  'description' => 'Ditambahkan ke setiap tag judul halaman.'],
            ['group' => 'general', 'key' => 'seo.meta_description',     'value' => 'Sebuah blog modern',   'type' => 'string',  'description' => 'Deskripsi meta default untuk halaman tanpa deskripsi.'],
            ['group' => 'general', 'key' => 'seo.og_image_url',         'value' => null,              'type' => 'string',  'description' => 'Gambar Open Graph default untuk berbagi di media sosial.'],
            ['group' => 'general', 'key' => 'seo.google_analytics_id',  'value' => null,              'type' => 'string',  'description' => 'ID pengukuran Google Analytics (GA-XXXXXX).'],
            ['group' => 'general', 'key' => 'seo.robots',               'value' => 'index,follow',    'type' => 'string',  'description' => 'Nilai meta tag robots default.'],
            ['group' => 'general', 'key' => 'seo.sitemap_enabled',      'value' => true,              'type' => 'boolean', 'description' => 'Aktifkan pembuatan peta situs XML secara otomatis.'],

            // Media
            ['group' => 'media', 'key' => 'media.max_upload_size_mb', 'value' => 10,                                       'type' => 'integer', 'description' => 'Ukuran unggahan file maksimal dalam megabyte.'],
            ['group' => 'media', 'key' => 'media.allowed_types',      'value' => ['jpg','jpeg','png','gif','webp','pdf','mp4'], 'type' => 'json',    'description' => 'Ekstensi file yang diizinkan untuk diunggah.'],
            ['group' => 'media', 'key' => 'media.image_quality',      'value' => 85,                                       'type' => 'integer', 'description' => 'Kualitas kompresi JPEG/WebP (1-100).'],
            ['group' => 'media', 'key' => 'media.disk',               'value' => 'public',                                 'type' => 'string',  'description' => 'Disk penyimpanan untuk unggahan (public atau s3).'],
            ['group' => 'media', 'key' => 'media.auto_optimize',      'value' => true,                                     'type' => 'boolean', 'description' => 'Otomatis optimasi gambar saat diunggah.'],

            // Mail
            ['group' => 'mail', 'key' => 'mail.use_custom_smtp', 'value' => false,             'type' => 'boolean', 'is_env' => false, 'description' => 'Gunakan SMTP kustom sebagai ganti bawaan .env.'],
            ['group' => 'mail', 'key' => 'mail.from_name',    'value' => 'Blog Saya',          'type' => 'string', 'is_env' => false, 'description' => 'Nama pengirim yang ditampilkan di email.'],
            ['group' => 'mail', 'key' => 'mail.from_email',   'value' => 'hello@domain.com', 'type' => 'string', 'is_env' => false, 'description' => 'Alamat email pengirim.'],
            ['group' => 'mail', 'key' => 'mail.driver',       'value' => 'smtp',             'type' => 'string', 'is_env' => false, 'description' => 'Driver transport email.'],
            ['group' => 'mail', 'key' => 'mail.host',         'value' => '127.0.0.1',        'type' => 'string',  'is_env' => false, 'description' => 'Host SMTP.'],
            ['group' => 'mail', 'key' => 'mail.port',         'value' => 1025,               'type' => 'integer', 'is_env' => false, 'description' => 'Port SMTP.'],
            ['group' => 'mail', 'key' => 'mail.username',     'value' => null,               'type' => 'string',  'is_env' => false, 'description' => 'Nama pengguna SMTP.'],
            ['group' => 'mail', 'key' => 'mail.password',     'value' => null,               'type' => 'string',  'is_env' => true, 'is_locked' => false, 'description' => 'Kata sandi SMTP.'],
            ['group' => 'mail', 'key' => 'mail.encryption',   'value' => 'tls',              'type' => 'string',  'is_env' => false, 'description' => 'Enkripsi SMTP (tls/ssl).'],

            // Security
            ['group' => 'security', 'key' => 'security.registration_enabled',       'value' => true,  'type' => 'boolean', 'description' => 'Izinkan pendaftaran pengguna baru.'],
            ['group' => 'security', 'key' => 'security.login_attempts',              'value' => 5,     'type' => 'integer', 'description' => 'Maksimal percobaan login yang gagal.'],
            ['group' => 'security', 'key' => 'security.session_lifetime',            'value' => 120,   'type' => 'integer', 'description' => 'Masa berlaku sesi dalam menit.'],
            ['group' => 'security', 'key' => 'security.captcha_enabled',             'value' => false, 'type' => 'boolean', 'description' => 'Aktifkan CAPTCHA pada login dan pendaftaran.'],
            ['group' => 'security', 'key' => 'security.maintenance_mode',            'value' => false, 'type' => 'boolean', 'description' => 'Tempatkan situs dalam mode perbaikan.'],
            ['group' => 'security', 'key' => 'security.maintenance_whitelist_ips',   'value' => [],    'type' => 'json',    'description' => 'IP yang mengabaikan mode perbaikan.'],
            ['group' => 'security', 'key' => 'security.force_https',                 'value' => false, 'type' => 'boolean', 'description' => 'Paksa pengalihan HTTPS untuk semua permintaan.'],

            // Appearance / Theme — Color System
            ['group' => 'general', 'key' => 'appearance.color_preset',       'value' => 'green',              'type' => 'string',  'description' => 'Skema warna bawaan (green/orange/blue/custom).'],
            ['group' => 'general', 'key' => 'appearance.primary_color',      'value' => '#15803D',            'type' => 'string',  'description' => 'Warna merek utama (hex) — dipakai tombol, link, focus.'],
            ['group' => 'general', 'key' => 'appearance.secondary_color',    'value' => '#0F766E',            'type' => 'string',  'description' => 'Warna merek sekunder (hex) — hover card, border, pendukung.'],
            ['group' => 'general', 'key' => 'appearance.accent_color',       'value' => '#D4A017',            'type' => 'string',  'description' => 'Warna aksen (hex) — sorotan, statistik, elemen spesial.'],
            ['group' => 'general', 'key' => 'appearance.heading_color',      'value' => '#0F172A',            'type' => 'string',  'description' => 'Warna teks judul/heading (hex).'],
            ['group' => 'general', 'key' => 'appearance.body_color',         'value' => '#334155',            'type' => 'string',  'description' => 'Warna teks isi/body (hex).'],
            ['group' => 'general', 'key' => 'appearance.muted_color',        'value' => '#64748B',            'type' => 'string',  'description' => 'Warna teks redup/muted (hex).'],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(
                ['key' => $data['key']],
                array_merge([
                    'group'       => $data['group'],
                    'type'        => $data['type'] ?? 'string',
                    'is_env'      => $data['is_env'] ?? false,
                    'is_locked'   => $data['is_locked'] ?? false,
                    'description' => $data['description'] ?? null,
                ], ['value' => $data['value']])
            );
        }

        $this->command->info('✅ Settings seeded: ' . count($settings) . ' entries across ' . count($groups) . ' groups.');
    }
}

