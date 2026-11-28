# Kontrak API — Advanced Room Filter & Sort (rencana)

GET /api/rooms, Bearer token Sanctum, rate limiting P12 tetap. Parameter opsional yang diusulkan:

| Parameter | Validasi | Semantik |
|---|---|---|
| building | string, max 255 | Nama gedung persis |
| min_capacity | integer, min 1 | Kapasitas >= nilai |
| sort | enum: capacity_asc, capacity_desc, available_from_asc, available_from_desc | Pengurutan server dengan ID sebagai tie-breaker |

Parameter lama search, gender, available, page, per_page tetap kompatibel; tanpa sort tetap orderBy id. Filter baru dikombinasikan AND. Respons 200 dengan status, message, data, meta tetap sama; hasil kosong data []; input tidak valid 422; tanpa token 401; rate limit 429. Tidak ada perubahan tabel atau resource.

Contoh: GET /api/rooms?building=Gedung%20A&min_capacity=2&sort=capacity_desc

Frontend: pertahankan pencarian lokal nama/gedung/gender/fasilitas serta opsi lama; hasil advanced filter API terpisah dari daftar global agar beranda dan favorit tidak terpengaruh. Reset filter memulihkan daftar default. API pagination harus tetap diikuti. Uji kombinasi, urutan, reset, input invalid, hasil kosong, dan lima flow baseline.
