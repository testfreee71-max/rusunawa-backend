\# P13 — Git Rollback Drill



\## 1. Informasi Proyek



\- Project: SiRUNA (RUSUNAWA)

\- Kelompok: Grup E-PJBL

\- Pelaksana: Yahdi Abdan

\- Branch: drill/rollback-p13

\- Baseline commit: 83d56b6



\## 2. Skenario Pengujian



| Skenario | Perintah | Hasil |

|---|---|---|

| Perubahan belum commit | git restore | PASS |

| Commit belum push | git reset --soft HEAD\~1 | PASS |

| Commit sudah push | git revert dan git push | PASS |



\## 3. Bukti Commit



\- 83d56b6 — Baseline rollback simulation.

\- 1c3d933 — Simulasi kesalahan setelah push.

\- ac028b0 — Revert commit kesalahan.



\## 4. Hasil Verifikasi



\- Working tree kembali bersih.

\- File simulasi kembali ke kondisi baseline.

\- Riwayat commit tetap terjaga pada remote repository.

\- Branch dev dan main tidak terganggu.

\- Tidak menggunakan force push.



\## 5. Bukti GitHub



https://github.com/testfreee71-max/rusunawa-backend/tree/drill/rollback-p13



\## 6. Dokumentasi Video



Nama video: P13\_Rollback\_Drill\_Grup\_E\_SiRUNA.mp4



Link folder video:



https://drive.google.com/drive/folders/1IOwXN\_j4xYnBCV4bEkX8-Zh5soqVsf1z?usp=sharing



\## 7. Kesimpulan



Ketiga skenario rollback berhasil dilaksanakan menggunakan branch simulasi terpisah tanpa mengganggu branch pengembangan utama.



Demonstrasi dilakukan melalui rekaman layar terminal oleh Yahdi Abdan. Penampilan seluruh anggota kelompok belum terpenuhi.

