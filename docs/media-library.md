# Dokumentasi Arsitektur Media Library

Dokumen ini menjelaskan bagaimana sistem manajemen aset (Media Library) bekerja di dalam *test-blog*, struktur penyimpanannya, serta bagaimana ia berinteraksi dengan entitas lain seperti *Post* dan *User*.

Sistem ini dibangun di atas pondasi **Spatie Media Library**, yang telah dimodifikasi dan disesuaikan secara khusus untuk mendukung kebutuhan *multi-user* dan memastikan integritas data (mencegah *broken links*).

---

## 1. Struktur Penyimpanan (*Path Generator*)

Sistem menggunakan kelas kustom `CustomPathGenerator` untuk mengatur letak penyimpanan fisik setiap *file* yang diunggah. Struktur ini memastikan pemisahan aset yang rapi berdasarkan siapa atau apa pemilik dari aset tersebut.

| Pemilik (Model) | Struktur Direktori Peladen (*Server*) | Contoh URL |
| :--- | :--- | :--- |
| **User** | `users/{user_id}/{collection}/{media_id}/` | `/storage/users/1/gallery/45/image.png` |
| **Post** | `posts/{post_id}/{media_id}/` | `/storage/posts/10/46/image.png` |
| **SystemAsset**| `systemassets/{collection}/{media_id}/` | `/storage/systemassets/default/47/logo.png` |

---

## 2. Visibilitas & *Media Picker*

Komponen *frontend* `MediaPicker.tsx` dan *backend* `MediaController.php` bekerja sama untuk menentukan gambar apa saja yang boleh dilihat oleh pengguna saat mereka membuka galeri.

**Aturan Visibilitas Galeri:**
- Pengguna hanya melihat aset yang **diunggah oleh dirinya sendiri** (`uploaded_by`).
- Pengguna dapat melihat aset yang ditandai sebagai **publik** (`is_public = true`).
- Pengguna dapat melihat aset yang **terikat pada Post aktif** (artikel yang belum dihapus/masuk tong sampah).
- **Filter Cerdas:** Jika sebuah *Post* dihapus sementara (*soft-deleted*), semua gambar milik *Post* tersebut akan otomatis disembunyikan dari galeri untuk menghindari penggunaan gambar "zombie".

---

## 3. Integrasi dengan *Post* (Artikel)

Hubungan antara Media Library dan Post dirancang sangat mutakhir dengan fokus utama pada **Integritas Konten**. Terdapat dua jenis aset di dalam Post: *Featured Image* (Thumbnail) dan *Inline Image* (Gambar di dalam Editor Teks).

### A. *Featured Image* (Gambar Utama)
Saat pengguna memilih gambar dari *Media Library* untuk dijadikan gambar utama artikel:
1. ID dari Media tersebut dikirim ke *backend*.
2. Sistem tidak sekadar menautkan ID tersebut, melainkan **Menyalin (Copy)** media fisik tersebut agar menjadi milik `Post`.
3. Salinan ini masuk ke dalam koleksi bernama `thumbnail` di direktori `posts/{post_id}/...`.

### B. *Inline Image* (Gambar Konten Editor)
Ini adalah mekanisme otomatis di belakang layar (*background mechanism*) yang sangat kuat.
1. Pengguna menyisipkan gambar dari *Media Library* (misalnya dari galeri *User* mereka) ke dalam teks editor TipTap.
2. Saat tombol **Save** ditekan, Laravel memindai teks HTML artikel tersebut.
3. **Auto-Copy:** Sistem akan mengekstrak semua URL gambar. Jika ia menemukan URL milik *User* atau *SystemAsset*, ia akan:
   - Menyalin *file* fisik tersebut agar menjadi milik *Post* (masuk ke koleksi `content_images`).
   - Mengubah *string* URL di dalam teks HTML agar mengarah ke URL salinan terbaru (`/storage/posts/...`).
4. **Garbage Collection (Pembersihan Otomatis):** Sistem kemudian memeriksa ulang seluruh gambar yang ada di koleksi `content_images`. Jika ada gambar di *database* yang URL-nya **tidak lagi ditemukan** di dalam teks HTML (berarti dihapus oleh pengguna dari teks editor), gambar fisik tersebut akan **dihapus permanen** dari peladen untuk menghemat ruang penyimpanan.

> [!IMPORTANT]
> **Mengapa disalin (di-copy) dan tidak sekadar ditautkan?**
> Hal ini dilakukan untuk menjamin keabadian konten publik. Jika kita hanya menautkan gambar dari galeri *User*, lalu suatu hari *User* tersebut menghapus akunnya atau menghapus gambar tersebut dari galerinya, maka gambar di artikel akan rusak (*Broken Link* / 404). Dengan menyalinnya ke *Post*, gambar tersebut resmi menjadi milik *Post* tersebut.

---

## 4. Siklus Hidup & Penghapusan

Karena aset disalin dan diikat pada model `Post`, maka aset tersebut mengikuti siklus hidup (*lifecycle*) artikelnya:

- **Soft Delete (Masuk Tong Sampah):** Jika artikel dihapus menggunakan tombol *Delete* biasa, artikel tersebut berstatus *Soft Deleted*. Gambar di dalam peladen **tidak akan dihapus**, agar saat artikel dipulihkan (*Restore*), gambar-gambarnya tetap utuh.
- **Force Delete (Dihapus Permanen):** Jika sistem secara permanen menghancurkan data artikel (`$post->forceDelete()`), maka fungsi pelacak dari Spatie (*MediaObserver*) akan ikut aktif dan **menghancurkan semua gambar fisik beserta folder** `posts/{id}` tanpa sisa sama sekali.

> [!TIP]
> Arsitektur ini memastikan peladen Anda bersih dari *file* yang tidak terpakai, sekaligus mengamankan semua artikel publik dari kehilangan gambar.

---

## 5. Pemecahan Masalah (Troubleshooting) & Multi-Domain

Saat menjalankan aplikasi ini dalam arsitektur multi-domain (contoh: `dashboard.test-blog.test` dan `blog.test-blog.test`), pengelolaan *file upload* dan *CORS (Cross-Origin Resource Sharing)* memegang peran krusial. Beberapa kendala yang pernah terjadi dan cara penyelesaiannya:

### A. Isu 403 Forbidden pada Akses Gambar
**Gejala:** Setelah mengunggah atau memperbarui *visibility* gambar, penampil (*viewer*) gagal memuat gambar dan melempar *HTTP 403 Forbidden*.
**Penyebab & Solusi:** 
- Terjadi perbedaan atau inkonsistensi skema URL (*absolute* vs *relative*). Solusi utamanya adalah selalu mereturn URL gambar sebagai *absolute path* mulai dari *root* (e.g., `/storage/...`) daripada beserta domainnya.
- Pastikan juga konfigurasi `cors.php` Laravel mengizinkan origin dari kedua domain, terutama pada jalur unggahan dan penyajian aset di `public/storage/`.

### B. Isu 500 Internal Server Error saat Upload
**Gejala:** Proses *upload* mengembalikan error 500 di *console*, meskipun *file* fisik berhasil terunggah (terlihat ketika di-reload). Log mencatat `FileNotFoundException: The file ".../Temp/phpXXXX.tmp" does not exist`.
**Penyebab & Solusi:**
- *Bug* ini sering terjadi pada pustaka *Spatie Media Library* ketika sebuah referensi *temporary uploaded file* masih digunakan atau di-render di dalam sebuah Blade / View Response *setelah* file sementara tersebut dipindahkan/diolah oleh *Spatie*.
- Hal ini umum terjadi apabila `MediaController` atau `ProfileController` melakukan pemanggilan `addMedia()` lalu mencoba merender ulang properti relasi `media` di dalam fungsi yang sama tanpa *reload* / pembaruan objek (dimana referensi asli file `$request->file('avatar')` masih tertahan di *memory* / *request payload*).
- **Perbaikan yang dilakukan:** Selalu memanggil *method* untuk mengembalikan struktur JSON sederhana dan spesifik (misal *URL image* yang baru, atau *refresh model*) setelah *file* berhasil disimpan, jangan me-return ulang seluruh *Request Payload* bawaan yang memuat objek file sementara.
