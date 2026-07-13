# Settings & Dynamic Branding

Aplikasi Modern Blog ini menggunakan sistem pengaturan (Settings) dinamis yang digerakkan oleh database untuk mengelola identitas, warna, SEO, dan fitur inti lainnya tanpa perlu mengubah kode sumber atau *environment variables* (.env) secara langsung.

## 1. Arsitektur Setting

Semua pengaturan disimpan di tabel `settings` dan dikelompokkan (group) dalam kategori seperti `general`, `media`, `mail`, dan `security`. 

* **Context System**: Pengaturan mendukung tingkatan konteks (`global`, `dashboard`, `blog`). Pengaturan pada spesifik konteks (seperti `dashboard`) akan menimpa (`override`) nilai yang ada pada level `global`. 
* **Database Seeder**: Nilai default pengaturan (seperti nama situs dan warna utama) diinisialisasi melalui `database/seeders/SettingSeeder.php`. Jika Anda perlu menambahkan key pengaturan baru yang persisten, tambahkan pada file seeder ini.

## 2. SettingService & Cache (Penting!)

Karena pengaturan diambil di setiap *request* halaman, kami menggunakan `\App\Services\SettingService` dengan implementasi caching intensif untuk performa tinggi.

* **Fetching Settings**:
  ```php
  // Mengambil 1 nilai
  $primaryColor = \App\Services\SettingService::get('appearance.primary_color', '#000000', 'global');
  
  // Mengambil 1 grup, menghapus awalan 'group.' pada keys
  $generalGroup = \App\Services\SettingService::group('general', 'dashboard');
  ```
* **Cache Invalidation (Pembersihan Cache)**:
  Setiap perubahan pada konfigurasi, khususnya lewat antarmuka admin, akan memicu `SettingService::forgetCache` dan `SettingService::forgetGroup`.
  **Catatan Penting**: Sistem memastikan *cache invalidation* dilakukan secara menyeluruh terhadap seluruh *context* (`global`, `dashboard`, `blog`). Hal ini memastikan apabila pengguna mengubah "Logo" pada pengaturan global, sidebar dashboard tidak menampilkan logo lama akibat tersangkut (*stuck*) di cache spesifik konteks dashboard.

## 3. Dynamic Branding & Custom Colors

Tampilan dan identitas aplikasi sepenuhnya bergantung pada konfigurasi dinamis.
1. **Warna Tema CSS Variables**:
   Warna utama (Primary) dan sekunder (Secondary) disuntikkan secara dinamis sebagai CSS variables `:root` ke seluruh layout HTML:
   * Frontend: `resources/views/web/layouts/app.blade.php`
   * Dashboard: `resources/views/dashboard.blade.php`
   * Inertia Frontend: `resources/views/app.blade.php`
   * Error Pages: `resources/views/errors/common.blade.php`
   
   Sistem CSS (`Tailwind v4`) akan merender warna ini tanpa hambatan meskipun dalam format kode *Hexadecimal* standar (`#6366f1`), dan secara otomatis menangani *opacity modifiers* (seperti `bg-primary/50`).

2. **Logo & Favicon**:
   Logo dan Favicon juga dikendalikan via *SettingService*.
   * Terdapat **Graceful Fallback**: Apabila `logo_url` belum diatur atau dikosongkan, komponen React `DashboardLayout` serta header website akan memproduksi "monogram" modern (memuat inisial pertama Nama Situs menggunakan warna primer aplikasi).

## 4. Media Picker Integration (Upload Logo/Favicon)

Form pengaturan (`resources/js/dashboard/Pages/Settings/Show.tsx`) memuat integrasi langsung ke komponen **MediaPicker**.
* Komponen ini memungkinkan pengguna memilih aset yang ada dari Media Library aplikasi atau mengunggah *file* gambar baru secara langsung (yang otomatis disimpan di storage dan dilacak di tabel media via model Spatie MediaLibrary `SystemAsset`).
* Backend `SettingController` secara cerdas mengidentifikasi nilai `.logo_url` yang berupa *instance* `UploadedFile` dan menyimpannya sebagai URL media lokal sebelum menyimpannya ke tabel `settings`.

## Menambahkan Pengaturan Baru (Panduan Singkat)

1. Tambahkan data *seeder* baru di `SettingSeeder.php` dengan parameter tipe yang tepat (misal `'type' => 'string'`).
2. Jika tipe string mengandung kata "color" dalam penamaan key, form frontend UI secara otomatis akan merendernya sebagai *Color Picker Input*.
3. Jika Anda memanggilnya di dalam `Blade`, gunakan `SettingService::get('group.key_name')`.
4. Jika dipanggil di `React / Inertia`, pengaturan disebarkan melalui prop `settings` dari `app/Http/Middleware/HandleInertiaRequests.php`.
