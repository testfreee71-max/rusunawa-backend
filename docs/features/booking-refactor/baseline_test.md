\# P13 — Booking Controller Refactoring



\## 1. Informasi Refactoring



\- Project: SiRUNA (RUSUNAWA)

\- Branch: refactor/booking-controller

\- Target branch: dev

\- Baseline commit: 1e67a49

\- Developer: Yahdi Abdan

\- Code smell: Long Method

\- Teknik: Extract Method

\- Target: RusunawaController.php

\- Method: createBooking()



\## 2. Tujuan



Meningkatkan readability, maintainability, dan separation of concerns pada method createBooking() tanpa mengubah perilaku API.



\## 3. Atomic Commit



| Commit | Perubahan |

|---|---|

| e6fb27a | Extract ensureRoomAvailable() |

| 2eaedba | Extract ensureNoOverlappingBooking() |

| 092c3fb | Extract ensureRoomCapacity() |



\## 4. Baseline Regression Testing



| Tahapan | Requests | Assertions | Failed | PHPUnit |

|---|---:|---:|---:|---:|

| Before | 28 | 103 | 0 | 14/14 |

| R01 | 28 | 103 | 0 | 14/14 |

| R02 | 28 | 103 | 0 | 14/14 |

| R03 | 28 | 103 | 0 | 14/14 |



Setiap pengujian PHPUnit menghasilkan 52 assertions tanpa kegagalan.



\## 5. Baseline Flow



\- F01: Registrasi pengguna — PASS

\- F02: Login pengguna — PASS

\- F03: Daftar kamar — PASS

\- F04: Pembuatan booking — PASS

\- F05: Logout dan pencabutan token — PASS



\## 6. Perilaku yang Dipertahankan



\- Database transaction.

\- Database row locking.

\- Validasi ketersediaan kamar.

\- Pencegahan double booking.

\- Validasi kapasitas kamar.

\- HTTP status code.

\- Pesan validasi.

\- Struktur respons API.



\## 7. Bukti Pengujian



\- baseline-before.txt

\- baseline-r01.txt

\- baseline-r02.txt

\- baseline-r03.txt



\## 8. Kesimpulan



Refactoring dilakukan melalui tiga atomic commit menggunakan teknik Extract Method. Seluruh regression test sebelum dan sesudah setiap commit berhasil tanpa kegagalan. Tidak ditemukan regresi pada skenario yang diuji.

