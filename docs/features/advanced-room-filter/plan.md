# P13 — Advanced Room Filter & Sort

Grup E-PJBL | RUSUNAWA

## Scope
Branch kedua repository: `feature/advanced-room-filter`. Tambahkan filter gedung, kapasitas minimum, dan pengurutan kapasitas/tanggal ketersediaan melalui API. Pertahankan pencarian fasilitas, filter lama, favorit, dan daftar kamar global pada beranda. Tidak ada perubahan skema.

## Task decomposition
| Task | File yang boleh diubah | Output |
|---|---|---|
| T01 | docs/features/advanced-room-filter/plan.md | Rencana |
| T02 | docs/features/advanced-room-filter/baseline_test.md | Baseline |
| T03 | docs/features/advanced-room-filter/api_contract.md | Kontrak |
| T04 | app/Http/Requests/Api/RoomListRequest.php | Validasi parameter |
| T05 | app/Http/Controllers/Api/RusunawaController.php | Query rooms saja |
| T06 | docs/features/advanced-room-filter/room-filter.postman_collection.json | Tes API |
| T07 | lib/repositories/room_repository.dart | Parameter query |
| T08 | lib/viewmodels/room_view_model.dart | State hasil filter terpisah |
| T09 | lib/widgets/room_filter_panel.dart | Kontrol filter |
| T10 | lib/screens/rooms/room_list_screen.dart | Integrasi UI |
| T11 | test/room_filter_test.dart | Tes fitur frontend |

Di setiap task, file lain tidak boleh disentuh tanpa revisi plan. Migration, auth, booking, konfigurasi P12 dan generated plugin files di luar scope. Satu prompt satu file/perubahan; review diff; uji fitur yang sudah tersedia dan 5 flow baseline setelah setiap commit. Catat hasil aktual dan hash. Minimal 6 commit atomic fitur, Conventional Commits (`docs(scope):`, `feat(scope):`, `test(scope):`). PR ke dev, review anggota dengan clone/run lokal, lalu merge dan verifikasi ulang. Karena dua repository, gunakan PR fitur di masing-masing repository yang berubah.

## Pembagian kerja
Yahdi Abdan: pelaksana utama seluruh task teknis. Kontribusi anggota lainnya belum dikonfirmasi. Persyaratan minimal satu commit per anggota dan review aktual masih harus dipenuhi.

## Rollback readiness
Gunakan branch drill terpisah dan file dummy. (1) Belum commit: git restore file dummy yang tracked. (2) Commit belum push: git reset --soft HEAD~1; bersihkan perubahan dummy jika diperlukan. (3) Sudah push: git revert HASH lalu git push. Jangan force-push dev/main. Simpan branch drill dan link video maksimal 2 menit di docs/features/rollback_drill.md. Yahdi demonstrator utama; partisipasi semua anggota wajib, belum dilakukan.

## Refactor berikutnya
Setelah fitur merged, branch refactor/booking-method. Code smell: createBooking() panjang. Extract helper untuk pengecekan ketersediaan, overlap dan kapasitas dalam minimal 3 commit refactor(booking):; pertahankan transaksi, urutan lock, pesan, status HTTP dan hasil. Baseline tiap commit; PR terpisah dengan review aktual.
