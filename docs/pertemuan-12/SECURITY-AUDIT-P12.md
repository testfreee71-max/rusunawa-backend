# Security Audit & Risk Triage — RUSUNAWA

**Pertemuan 12 | 28 November 2026 | Backend Laravel + Sanctum**

Baseline: `6bebb45`, tag `baseline-pertemuan-12`. Branch: `fix/pertemuan-12-security`. Lingkup: API lokal dan source code backend yang diperiksa; bukan audit penetrasi produksi.

## 1. Cakupan audit

| Kategori | Hasil pemeriksaan |
|---|---|
| Dependency | Composer audit tidak menemukan advisory pada saat pemeriksaan. |
| Authentication | Sanctum melindungi 19 dari 21 route; login dan register publik dengan throttle 10/menit. |
| Authorization | Student ditolak saat membuat, mengubah, menghapus kamar (403). |
| Object-level authorization | Booking, favorit, dan notifikasi dibatasi berdasarkan pengguna pada controller; uji lintas akun belum dilakukan. |
| Input validation | Form Request tersedia; ditemukan batas array yang belum ditetapkan dan typo rule name. |
| Mass assignment | Role tidak masuk fillable User; input booking menggunakan validated data. |
| Rate limiting | Sebelum fix tidak ada throttle eksplisit pada route privat. |
| Session/token management | Masa berlaku token sebelumnya null; logout mencabut token aktif. |
| Configuration & secrets | Debug lokal aktif, HTTP lokal, .env diabaikan Git. |
| Information exposure | Header X-Powered-By mengungkap versi PHP; respons API diuji terhadap data sensitif. |

## 2. Temuan dan risk triage

Prioritas bersifat kualitatif berdasarkan dampak dan kemungkinan pada lingkup yang diperiksa, bukan skor CVSS. Risiko deployment merupakan kondisi potensial, bukan kerentanan produksi yang telah terbukti.

| ID | Temuan | Risiko | Bukti / dasar | Status |
|---|---|---|---|---|
| SEC-01 | Token tidak kedaluwarsa | High | Konfigurasi null; token 48 jam HTTP 200 | FIX-01: 1440 menit; HTTP 401 |
| SEC-02 | Route privat tanpa throttle eksplisit | High | 65 request HTTP 200 | FIX-02: 60/menit; 429 terbukti |
| SEC-03 | Token dapat menumpuk | Medium | Login membuat token baru; akun demo memiliki 7 token | Mitigasi parsial oleh expiration; pruning belum diverifikasi |
| SEC-04 | APP_DEBUG=true | Medium jika terekspos | Konfigurasi lokal dan .env.example | Hardening saat deployment |
| SEC-05 | Password demo hardcoded | Low pada lokal | Seeder dibatasi local/testing | Jangan gunakan akun demo di produksi |
| SEC-06 | HTTP tanpa TLS | High jika produksi | URL lokal HTTP | Terapkan HTTPS saat hosting |
| SEC-07 | Versi PHP pada header | Low | X-Powered-By: PHP/8.3.37 | Hardening server |
| SEC-08 | Jumlah gambar tidak dibatasi | Medium | Validator menerima 50 gambar | FIX-03: max 10 |
| SEC-09 | Jumlah fasilitas tidak dibatasi | Medium | Validator menerima 100 fasilitas | FIX-03: max 30 |
| SEC-10 | Kapasitas kamar tanpa batas maksimum | Low | Rule integer|min:1 | Evaluasi batas bisnis |
| SEC-11 | Typo rule name ma:255 pada baseline Git | Medium | Git diff RoomRequest.php; potensi error validasi, belum diuji terpisah | Dikoreksi menjadi max:255 bersama FIX-03 |

## 3. Baseline snapshot

Lima fitur utama: authentication, profile, rooms, bookings, favorites. Seluruhnya lulus. Notification dan authorization juga diperiksa. Run pertama: 28 request, 103 assertions, 14 gagal pada kelompok notifikasi akibat password akun demo tidak sesuai. Hash::check menghasilkan false. Password demo dipulihkan tanpa perubahan source code; retest notifikasi 19/19 assertions lulus. Baseline final sebelum security fix: 28 request, 103 assertions, 0 gagal, 21,3 detik. PHPUnit bawaan: 2/2 lulus (bukan pengujian fitur keamanan).

## 4. Tiga perbaikan prioritas

| Fix | File | Perubahan | Sebelum | Sesudah |
|---|---|---|---|---|
| FIX-01 | config/sanctum.php | expiration 1440 menit | Token 48 jam: 200 | 401 |
| FIX-02 | routes/api.php | auth:sanctum dan throttle:60,1 | 65 diterima | 60 diterima, 5 ditolak 429 (uji terisolasi) |
| FIX-03 | app/Http/Requests/Api/RoomRequest.php | images max:10, facilities max:30; koreksi typo name | Array berlebihan lolos validator | Ditolak validator; Postman 422 untuk kedua field |

Batas 24 jam, 60 request/menit, 10 gambar dan 30 fasilitas adalah kebijakan yang dipilih untuk proyek ini. Token lama dapat memerlukan login ulang.

## 5. Postman attack tests dan regression

| Skenario | Hasil aktual setelah fix |
|---|---|
| Token berusia 48 jam | HTTP 401 |
| Tanpa token | HTTP 401 |
| Student update kamar | HTTP 403 |
| Input array berlebihan | HTTP 422, kedua array ditolak |
| Rate limit probe | 59 diterima, 6 ditolak HTTP 429; satu request admin sebelumnya menggunakan kuota |

Newman attack tests: 72 request, 74 assertions, 0 gagal, 48,5 detik. Regression: 28 request, 103 assertions, 0 gagal, 21,7 detik. PHPUnit bawaan: 2/2 lulus. Pengujian sebelum fix menggunakan HTTP melalui Tinker dan validator langsung; collection Postman dijalankan setelah fix. Tidak diklaim bahwa seluruh risiko telah dihilangkan.

## 6. Indeks bukti

- evidence/baseline-commit.txt, composer-audit.txt, auth-route-review.txt, migration-status.txt, laravel-version.txt
- evidence/baseline-before-fix.txt, baseline-notifications-retest.txt, baseline-final.txt
- evidence/sanctum-before.txt, token-expiration-before.txt, routes-before.txt, validation-before.txt
- evidence/attack-token-before.txt, attack-token-after.txt, attack-rate-before.txt, attack-rate-after.txt, attack-validation-before.txt, attack-validation-after.txt
- evidence/regression-after-fix.txt, postman-attack-results.txt
- evidence/01-baseline-before-fix.png, 02-regression-after-fix.png, 03-postman-attack-tests.png
- attack-tests/RUSUNAWA-P12-Attack.postman_collection.json

Laporan JSON mentah dan token pengujian tidak disertakan karena berpotensi mengandung kredensial. Bukti token sebelum fix berupa catatan hasil HTTP yang direkam dari terminal. Pembersihan akun pengujian dan finalisasi Git dilakukan terpisah.
