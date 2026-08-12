# SEO & Sitemap Architecture

Sistem SEO dan Sitemap pada aplikasi ini dirancang secara terpisah antara domain **Main Landing** dan domain **Blog** untuk menjaga agar masing-masing memiliki indeks yang bersih dan spesifik.

## 1. Struktur Folder Views

Semua *view* terkait SEO (seperti sitemap XML, RSS, dan stylesheet XSLT) kini ditempatkan secara modular di dalam direktori `resources/views/web/seo/`:

- **`landing/`**
  Folder ini berisi sitemap khusus untuk Main Landing page.
  - `index.blade.php`: Merupakan *Sitemap Index* yang menggabungkan seluruh sitemap landing page.
  - `profil.blade.php`, `pendidikan.blade.php`, `layanan.blade.php`, `media.blade.php`: Sitemap untuk masing-masing seksi (halaman).
  - `xsl.blade.php`: Template dinamis penata gaya (XSLT) khusus untuk sitemap di domain landing.

- **`blog/`**
  Folder ini berisi sitemap khusus untuk Blog.
  - `index.blade.php`: Merupakan *Sitemap Index* yang menggabungkan seluruh sitemap blog.
  - `categories.blade.php`, `pages.blade.php`, `posts.blade.php`, `tags.blade.php`: Sitemap spesifik bagian-bagian blog.
  - `rss.blade.php`: Feed RSS untuk blog.
  - `xsl.blade.php`: Template dinamis penata gaya (XSLT) khusus untuk sitemap di domain blog.

## 2. Dynamic XSLT Styling

File XSLT (`sitemap-landing.xsl` dan `sitemap-blog.xsl`) tidak lagi disimpan sebagai aset statis di direktori `public/`. Sebaliknya, file-file tersebut diubah menjadi *Blade Views* agar dapat dieksekusi secara dinamis oleh Laravel.

Hal ini memungkinkan sitemap untuk membaca langsung dari `SettingService`:
- **Nama Situs (`general.site_name`)**: Judul sitemap XML menyesuaikan dengan nama website secara dinamis (mengikuti domain context yang sedang aktif: `landing` atau `blog`).
- **Favicon (`general.favicon_url`)**: Memastikan logo/favicon yang dirender di XML Sitemap sama dengan yang tampil di *frontend* website.

## 3. Rute dan Controller

Rute sitemap didefinisikan secara independen di `routes/projects/landing.php` dan `routes/projects/blog.php`. Keduanya diarahkan ke `SitemapController` dengan metode penanganan masing-masing:
- **`landingIndex()` & `landingSection($section)`**: Menghasilkan XML untuk Landing.
- **`landingXsl()`**: Merender file XSLT dinamis untuk tampilan sitemap Landing.
- **`__invoke()` (Index Blog), `posts()`, `pages()`, `categories()`, `tags()`**: Menghasilkan XML untuk Blog.
- **`blogXsl()`**: Merender file XSLT dinamis untuk tampilan sitemap Blog.

## 4. Penghapusan Halaman Non-Eksis

Pastikan Anda hanya mendaftarkan URL yang sudah ada halaman/view-nya. Sebelumnya ada URL statis seperti `konsultasi-keagamaan`, `sewa-fasilitas`, dan `koperasi-kantin` yang sengaja dihapus dari `layanan.blade.php` karena *view* atau *route handler* untuk rute spesifik tersebut belum dibuat. URL yang merujuk pada halaman kosong (Error 404) akan mengganggu kualitas SEO (Search Engine Optimization) website.
