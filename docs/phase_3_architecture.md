# Phase 3 Architecture: Bookmarks & Reading History

Arsitektur untuk Fase 3 disusun berurutan dari layer *Database* hingga lapis *Resource* API dengan isolasi logika murni (Clean Architecture) pada *Service Layer*.

## Phase 3A � Database Foundation
**1. Bookmarks**
Tabel: Bookmarks
- id (uuid)
- post_id (uuid, FK)
- identity_key (string, INDEX)
- user_id (uuid nullable, FK)
- created_at, updated_at
- Constraint: UNIQUE(post_id, identity_key), INDEX(identity_key)

**2. Reading Histories**
Tabel: 
eading_histories
- id (uuid)
- post_id (uuid, FK)
- identity_key (string)
- user_id (uuid nullable, FK)
- First_read_at
- last_read_at
- 
ead_count
- created_at, updated_at
- Constraint: UNIQUE(post_id, identity_key)
- Index: INDEX(identity_key), INDEX(identity_key, last_read_at DESC) (dioptimasi untuk list riwayat baca berurut waktu)

## Phase 3B  Models
Model Eloquent standar: Bookmark dan ReadingHistory dengan relasi BelongsTo ke Post dan User.
Model Post ditambahkan relasi hasMany ke Bookmarks dan ReadingHistories.

## Phase 3C  Services
Logika bisnis utama diisolasi untuk memastikan Controller tetap bersih (Clean Controllers).

**1. BookmarkService**
- 	oggle(Post, IdentityData)
- isBookmarked(Post, IdentityData)
- BookmarksForIdentity(IdentityData)

**2. ReadingHistoryService**
- 
ecordRead(Post, IdentityData) -> **PENTING**: Di sini implementasi Raw SQL Upsert akan terjadi untuk performa 1-query: INSERT ... ON DUPLICATE KEY UPDATE read_count = read_count + 1, last_read_at = VALUES(last_read_at)
- 
ecentHistory(IdentityData, Limit)
- clearHistory(IdentityData)

## Phase 3D � Identity Migration Service
Mengekstraksi logika migrasi identitas *Guest* ke dalam service terpisah (IdentityMigrationService) agar MigrateGuestDataToUser Listener hanya bertindak sebagai pemanggil (Caller). Service ini akan menampung migrasi tabel:
- Reactions
- PostViews
- Bookmarks
- ReadingHistories

## Phase 3E � Controller
Controller untuk mengelola HTTP Request tanpa kebocoran logika basis data.
- **BookmarkController** (ex: PUT /posts/{slug}/bookmark) -> Menyelesaikan identitas -> Memanggil BookmarkService -> Mengembalikan *Resource*.

## Phase 3F � API Resources
*Serializers* seperti BookmarkResource dan ReadingHistoryResource untuk menjaga konsistensi format respons ke Frontend (Inertia/Next.js/Mobile App).
