# ModernBlog Master Roadmap

Proyek ini bertujuan menjadi sistem manajemen konten (CMS) kelas *Enterprise* dengan fungsionalitas dan arsitektur yang sanggup bersaing dengan Ghost, Hashnode, dan WordPress modern. Pengembangan dilakukan secara iteratif dalam beberapa fase terfokus.

## Phase 1 — MVP Foundation (Selesai)
- Sistem Autentikasi & Otorisasi (*Roles & Permissions*).
- Manajemen Artikel (*Posts*), Kategori (*Categories*), dan Label (*Tags*).
- Media Library Dasar.
- API *Resources* & *Dashboard* Web dasar.

## Phase 2 — Analytics & Reactions (Selesai)
- IdentityService & CrawlerService untuk pendataan lalu lintas bersih tanpa distorsi.
- Sistem *View Count* atomik (*Atomic Unique Tracking* 24 Jam via Cache).
- Sistem *Reactions* ganda (*Guest* dan *User*) dengan struktur *Atomic Lock* untuk menjaga dari data ganda.
- Transisi rekaman *Guest* ke *User* saat melakukan login.
- Fitur stabilisasi (*CLI Command*) cms:repair dan cms:doctor.
- Audit Trails (Riwayat Aktivitas).

## Phase 3 — Personalization (Bookmarks & Reading History)
Fokus pada retensi pengunjung dengan fitur personalisasi bacaan baik untuk *User* maupun *Guest*.
- **Bookmarks**: Menyimpan artikel yang diminati.
- **Reading History**: Riwayat artikel yang telah dilihat, dengan model *upsert* atomik di database.

## Phase 4 — Engagement
Memfasilitasi interaksi yang lebih dalam dengan pengunjung dan membangun komunitas.
- Comments (Sistem Komentar)
- Comment Reactions (Reaksi pada Komentar)
- Nested Replies (Balasan berlapis)
- Mentions (Penandaan Pengguna: @username)
- Notifications (Sistem Notifikasi internal dan eksternal)

## Phase 5 — Discovery
Meningkatkan kemudahan pengunjung menemukan konten yang relevan dan viral.
- Popular Score (Kalkulasi skor popularitas artikel dengan sistem peluruhan berdasar waktu)
- Trending Algorithm (Algoritma tren harian/mingguan)
- Search Analytics (Merekam dan menambang pencarian pengunjung)
- Related Posts (Saran artikel serupa berbasis tag/kategori atau vektor AI)
- Personalized Recommendation (Saran berdasarkan riwayat baca pengguna)

## Phase 6 — Editorial
Memperkaya alur kerja penyuntingan untuk *publishers* atau majalah berskala besar.
- Scheduled Publishing (Penjadwalan publikasi)
- Content Workflow (Alur kerja draf: *Draft* -> *Review* -> *Published*)
- Draft Review (Tinjauan artikel rahasia melalui tautan khusus tanpa publikasi)
- Revision History (Versi draf masa lalu untuk *rollback*)
- Editorial Calendar (Kalender Perencanaan Editorial)

## Phase 7 — Enterprise CMS
Mematangkan platform untuk adopsi berskala masif dengan performa tingkat atas.
- Media Library *Advanced* (Pengelolaan *folders*, galeri kustom)
- Collections & Series (Pengelompokan artikel berseri)
- Newsletters (Surat Edaran Berkala / Berlangganan)
- Multi-site (Pengelolaan banyak situs/domain di 1 CMS)
- Webhooks (Integrasi pemicu sistem ke pihak ketiga)
- Public API & GraphQL API (API Akses *Headless* untuk konsumsi luas)
- Activity Dashboard (Wawasan visual lalu lintas)
- Real-time Analytics (Menggunakan WebSockets/Redis untuk pemantauan *live*)
