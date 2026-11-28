# Baseline P13 — 28 November 2026

Backend dev: `5fe599b910eff295bfec04500beb4bf2c90ee9b1`
Frontend dev: `95b98c0c862dee13cecd39fa40bbbd5b7b8eb33a`

| Flow | Expected | API baseline | Bukti frontend |
|---|---|---|---|
| F1 Registrasi | Akun dibuat | PASS | Form dan profil akun baru |
| F2 Login | Akses akun | PASS | Form login dan daftar kamar/profil setelah login |
| F3 List kamar | Data tampil | PASS | Daftar kamar |
| F4 Booking | Booking tercipta | PASS | Booking berhasil ID 13, menunggu persetujuan |
| F5 Logout | Token ditolak setelah logout | PASS | Kembali ke layar login; invalidasi token dibuktikan oleh API |

Newman: 28 requests, 103 assertions, 0 failed, 22.7 s. Log asli: baseline-before.txt. Flutter analyze: 30 info, 0 error; flutter test belum memiliki file test. Android emulator berhasil menjalankan aplikasi. Bukti screenshot perlu menyensor data pribadi sebelum publikasi.

## Log iterasi
Tambahkan baris setiap commit dengan hash, F1–F5, hasil tes fitur, dan lokasi bukti. Jangan isi PASS sebelum pengujian dilakukan.

| Commit | F1 | F2 | F3 | F4 | F5 | Bukti |
|---|---|---|---|---|---|---|
| Baseline backend 5fe599b | PASS | PASS | PASS | PASS | PASS | baseline-before.txt |
