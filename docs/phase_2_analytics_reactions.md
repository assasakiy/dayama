# Phase 2 Architecture: Analytics & Reactions

Dokumentasi ini mencatat arsitektur dan keputusan teknis yang telah diimplementasikan pada Fase 2D (Stabilization & Architecture Hardening) untuk sistem Analytics (Post Views) dan Reactions.

## 1. Konsep Identitas Tunggal (Single Identity)
Masalah duplikasi data dan tracking guest yang tidak akurat diselesaikan dengan pendekatan *Single Source of Truth* untuk identitas:
- **IdentityService**: Kelas terpusat (App\Services\IdentityService::current()) yang mendeteksi dan mengembalikan array berisi key dan user_id.
- **Identity Key**: Kunci deterministik yang disimpan di database.
  - Untuk pengguna login: user:{uuid}
  - Untuk pengguna anonim/guest: guest:{visitor_token}

## 2. Crawler & Bot Detection
Menghindari polusi data analitik akibat *traffic* bot/crawler.
- **CrawlerService**: Membungkus library jaybizzle/crawler-detect.
- **Middleware EnsureVisitorToken**: Mengecek *User-Agent* dengan metode isCrawler(). Jika terdeteksi sebagai bot, middleware akan melakukan *early-return* untuk melewati pembuatan *cookie* isitor_token.
- **Controller/Service Layer**: Pencatatan (insert) post_views diabaikan bila pengunjung adalah bot.

## 3. Sistem "Views" Atomik (Post Views)
Pencegahan duplikasi atau spam *refresh* beruntun (mis. *10x refresh unique view test*).
- **Atomic Locking via Cache**: Menggunakan Cache::add() dengan key khusus (misal iew:{post_id}:{identity_key}) dan TTL 24 jam. Ini memastikan logika basis data hanya terpanggil 1 kali dalam 1 hari per pengunjung per artikel.
- **Database Unique Constraint**: Kombinasi UNIQUE(post_id, identity_key, view_date) pada tabel post_views bertindak sebagai pertahanan lapis kedua yang tidak dapat ditembus (Hard Constraint).

## 4. Sistem Toggle Reaksi (Reactions)
- Reaksi bersifat *hard-delete* (permanen) ketimbang *soft-delete* untuk memudahkan integritas unik.
- Jika tipe reaksi yang dikirim sama dengan tipe yang ada di database, sistem melakukan aksi *Toggle* (penghapusan data).
- Jika tipenya berbeda, baris diupdate (misal 'like' menjadi 'dislike').
- Constraint: UNIQUE(post_id, identity_key, type) menjaga agar satu orang tidak bisa memberikan dua reaksi dengan tipe yang sama pada satu artikel.

## 5. Sinkronisasi Data (Data Integrity)
- **Observer (ReactionObserver)**: Mengubah pendekatan dari manual increment/decrement (+1/-1) yang rawan *race-condition* menjadi **Full Recount** menggunakan fungsi agregasi SQL (GROUP BY). Pendekatan ini mengutamakan *Correctness over Micro Performance*.
- **MigrateGuestDataToUser**: Sebuah event listener yang otomatis mendeteksi saat *Guest* mendaftar atau *Login*. Data pada tabel post_views dan eactions yang sebelumnya memiliki status guest:{token} akan diperbarui menjadi user:{uuid} secara masif tanpa menciptakan konflik.
- **CMS Commands**: 
  - php artisan cms:doctor : Memeriksa kesehatan data dan menemukan anomali (orphans atau ketidakcocokan jumlah views/reactions).
  - php artisan cms:repair : Memperbaiki otomatis penghitungan *cache* yang *drift* atau korup.
