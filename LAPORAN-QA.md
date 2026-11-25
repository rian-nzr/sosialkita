# Laporan Pengujian (QA) — SosialKita

| | |
|---|---|
| **Proyek** | SosialKita — website media sosial/forum (PHP + MySQL, tanpa framework) |
| **Tanggal pengujian** | 26 November 2026, 01:00–01:26 WIB |
| **Penguji** | QA browser (Chrome, dijalankan di laptop pengembang) + verifikasi kode oleh pengembang |
| **Lingkungan uji** | `http://127.0.0.1:8000/` (PHP built-in server + MySQL di laptop), Chrome desktop ~1456px, simulasi lebar HP (zoom 300%) |
| **Kode yang diuji** | Commit `b25dbd32` (mode gelap) s.d. `15119588` (perbaikan redirect loop); dua `git pull` terjadi di tengah pengujian |
| **Data awal** | 3 pengguna (admin, demo, rani), 4 postingan, 1 komentar, 3 like (setelah pembersihan) |

> Laporan ini ditulis setelah pengujian. Temuan yang sudah diperbaiki dicatat
> beserta commit perbaikannya. Bagian 6 mencatat temuan QA yang **tidak terbukti**
> setelah dicek langsung di kode — dibaca sebagai koreksi, bukan sebagai bug.

---

## 1. Ringkasan Eksekutif

Total **30 test case** dijalankan: **29 lolos**, **1 gagal** saat diuji
(proteksi admin — redirect loop). Dari pengujian ditemukan **1 temuan Kritis**,
**3 temuan Mayor**, dan **8 temuan Minor**.

- **1 temuan Kritis dan 1 temuan Mayor sudah diperbaiki** di kode (lihat Bagian 5).
- **1 temuan Mayor masih terbuka** dan butuh pemeriksaan di laptop penguji:
  satu-satunya foto postingan tidak tampil (kemungkinan file rusak/hilang).
- **Sisa temuan Minor sudah diperbaiki** dalam commit yang sama dengan laporan ini,
  kecuali 2 item yang diputuskan sebagai data/contoh (lihat Bagian 5).

**Kesimpulan:** fungsionalitas inti (posting, like, komentar, cari, profil,
admin) bekerja dengan baik. Setelah perbaikan di Bagian 5 dites ulang secara
live, aplikasi layak diajukan sebagai tugas akhir.

---

## 2. Ruang Lingkup Pengujian

| Area | Diuji |
|---|---|
| Autentikasi | Login (benar/salah), logout, register, proteksi halaman admin |
| Feed | Muat feed, composer posting teks, like/unlike, detail postingan |
| Komentar | Tambah, edit (label "diedit"), hapus, hitungan komentar |
| Postingan | Edit, hapus, label "diedit", toast setelah hapus |
| Profil | Profil sendiri (edit bio), profil orang lain (tanpa kontrol edit), grid postingan |
| Pencarian & Explore | Cari pengguna, cari postingan, state kosong (Disarankan + Jelajahi) |
| Aktivitas | Daftar like & komentar di postingan sendiri |
| Admin | Dashboard, Kelola Postingan/Komentar/Pengguna, ubah role, hapus user |
| Tampilan | Mode gelap di semua halaman, responsif (simulasi lebar HP), nav pill bawah |
| Keamanan dasar | Masking password, proteksi admin dari akun biasa |

**Di luar lingkup:** hosting/publikasi (menunggu instruksi dosen), performa
beban, dan pengujian di browser selain Chrome.

---

## 3. Akun & Data Tes

| Akun | Peran |
|---|---|
| `admin@sosialkita.test` / `admin123` | Admin |
| `demo@sosialkita.test` / `demo123` | User biasa |
| `rani@sosialkita.test` / `rani123` | User biasa |

Data tes dibuat dengan awalan `[QA]` (1 postingan, 1 komentar, 1 akun
`qatester` / `qa@sosialkita.test`) dan **sudah dihapus/dibersihkan** setelah
pengujian: postingan & komentar `[QA]` dihapus, bio demo dikembalikan ke
"Halo! Ini akun demo.", akun `qatester` dihapus lewat halaman admin.
Tidak ada konten asli pengguna, akun admin, atau role admin yang diubah.

---

## 4. Matriks Test Case

Keterangan hasil: ✅ Lolos · ❌ Gagal · ⚠️ Lolos dengan catatan.

| ID | Modul / Fitur | Langkah Uji | Hasil |
|---|---|---|---|
| TC-01 | Feed (guest) | Buka beranda tanpa login | ✅ Feed tampil, tema gelap, nav pill lengkap |
| TC-02 | Login | Login sebagai demo | ✅ Masuk, toast "Selamat datang kembali, Demo User!" |
| TC-03 | Login | Password salah | ✅ Error merah "Email atau password salah.", field password dikosongkan |
| TC-04 | Logout | Logout (2x) | ✅ Dialihkan ke login.php |
| TC-05 | Register | Buat akun `[QA] Tester` / `qatester` | ✅ Berhasil, auto-login, profil kosong "Belum ada postingan." |
| TC-06 | Composer | Posting teks `[QA]` | ✅ Tayang di puncak feed + toast sukses |
| TC-07 | Like | Like lalu unlike postingan `[QA]` | ✅ "1 suka" + hati merah terisi, hilang setelah unlike, bertahan setelah reload |
| TC-08 | Detail postingan | Buka `post.php?id=5` | ✅ Judul "Komentar (1)" tampil setelah komentar ditambah |
| TC-09 | Komentar | Tambah → edit → hapus | ✅ Hitungan 1 → 0, label "diedit", toast "Komentar berhasil dihapus." |
| TC-10 | Edit postingan | Edit caption lewat `edit_post.php` | ✅ Tersimpan + label "diedit" |
| TC-11 | Hapus postingan | Hapus postingan `[QA]` | ✅ Postingan hilang, feed kembali ke 4 postingan |
| TC-12 | Profil / bio | Ubah bio lalu kembalikan | ✅ "Bio berhasil disimpan.", bio kembali ke semula |
| TC-13 | Cari pengguna | Cari "rani", "demo" | ✅ Masing-masing 1 pengguna ditemukan |
| TC-14 | Cari postingan | Cari "datang", "langitnya" (tengah kalimat) | ✅ Substring search bekerja (1 hasil tiap kata) |
| TC-15 | Explore | Buka `search.php` tanpa query | ✅ "Disarankan untuk Anda" + grid "Jelajahi" tampil |
| TC-16 | Profil orang lain | Buka `profile.php?u=rani` | ✅ Bio + 1 postingan + grid tampil, tanpa kontrol edit |
| TC-17 | Aktivitas | Buka `activity.php` | ✅ 3 item (like + komentar di postingan sendiri) |
| TC-18 | Mode gelap | Jelajahi semua halaman | ✅ Semua halaman gelap, kontras terbaca, tanpa bagian putih |
| TC-19 | Password | Ketik password di login | ✅ Input tampil sebagai titik-titik (ter-masking) |
| TC-20 | Admin login | Login sebagai admin | ✅ Langsung dialihkan ke `/admin/index.php` |
| TC-21 | Dashboard admin | Buka Dashboard Admin | ✅ Statistik (3 Pengguna, 4 Postingan, 1 Komentar, 3 Like) + daftar terbaru rapi |
| TC-22 | Admin Postingan | Buka + cari postingan | ✅ Tabel + kotak cari tampil, aksi Lihat/Hapus bekerja |
| TC-23 | Admin Komentar | Buka halaman Komentar | ✅ Tabel 1 komentar tampil rapi |
| TC-24 | Admin Pengguna | Buka halaman Pengguna | ✅ Tabel tampil; baris admin sendiri "—" (tidak bisa hapus/demote diri sendiri) |
| TC-25 | Ubah role | User → admin → user | ✅ Berubah dan bertahan (persist) |
| TC-26 | Hapus user | Hapus `qatester` lewat admin | ✅ Terhapus + toast sukses; pengguna 4 → 3 |
| TC-27 | Navigasi admin | Profil admin | ✅ Ada tombol "🛡️ Dashboard Admin" |
| TC-28 | Responsif (simulasi) | Zoom 300% (~lebar HP) | ⚠️ Feed, profil, login, search, dashboard & tabel admin muat tanpa overflow horizontal |
| TC-29 | Pratinjau foto | Pilih foto di composer & edit postingan | ✅ Pratinjau tampil sebelum upload + tombol ✕ membatalkan (tes render statis oleh pengembang) |
| TC-30 | Proteksi admin | Buka `/admin/index.php` sebagai demo | ❌ **Gagal** — `ERR_TOO_MANY_REDIRECTS` (lihat BUG-01) |

---

## 5. Daftar Temuan

### 5.1 Kritis

| ID | Temuan | Repro | Status |
|---|---|---|---|
| **BUG-01** | **Proteksi admin redirect loop.** Buka `/admin/index.php` sebagai user biasa menghasilkan halaman error Chrome `ERR_TOO_MANY_REDIRECTS`, bukan dialihkan ke beranda. Konten dashboard tidak bocor, dan pesan "Halaman ini khusus admin." sempat muncul setelah menekan Back, tetapi pengguna harus menekan Back untuk keluar. | Login sebagai demo → ketik `/admin/index.php` di address bar | ✅ **Diperbaiki** — commit `15119588`. Penyebab: `requireAdmin()` redirect relatif `Location: index.php` yang dari dalam `/admin/` diartikan browser sebagai `/admin/index.php` (redirect ke diri sendiri). Perbaikan: redirect absolut dari root (`/index.php`, `/login.php`). **Perlu tes ulang live.** |

### 5.2 Mayor

| ID | Temuan | Repro | Status |
|---|---|---|---|
| **BUG-02** | **Foto postingan tidak tampil.** Postingan demo id=4 (`uploads/20261124_152543_c766f7f4c5cb.png`) tampil sebagai area hitam kosong + teks alt, di feed, detail postingan, grid profil, dan grid Explore. Ini satu-satunya postingan berfoto, jadi semua tampilan foto praktis rusak. | Buka feed atau `post.php?id=4` | 🔴 **Terbuka** — file hanya ada di laptop penguji. Periksa: file masih ada di folder `uploads/`? Ukurannya normal? Bisa dibuka langsung di browser? |
| **BUG-03** | **Ubah role tanpa konfirmasi.** Tombol "Jadikan admin/user" di Kelola Pengguna langsung mengeksekusi tanpa dialog konfirmasi (satu klik salah bisa menaikkan hak akses). | Admin → Pengguna → klik "Jadikan admin" | ✅ **Diperbaiki** — commit perbaikan QA (dialog `confirm()` dengan teks sesuai arah perubahan role). |
| **BUG-04** | **Toast menyesatkan setelah hapus postingan.** Setelah menghapus postingan sendiri, toast berbunyi "Postingan tidak ditemukan." padahal penghapusan berhasil. | Hapus postingan sendiri dari halaman detail | ✅ **Diperbaiki** — commit `735f6ee4` (toast baru + pesan yang menunggu tidak ditimpa pesan berikutnya). **Perlu tes ulang live.** |

### 5.3 Minor

| ID | Temuan | Status |
|---|---|---|
| **BUG-05** | Nav pill bawah sementara menutupi konten saat scroll (baris like/komentar, input komentar, baris dashboard). Di ujung scroll konten terlihat penuh — tidak memblokir permanen. | ✅ **Diperbaiki** — padding bawah `main.container` 100px → 150px. |
| **BUG-06** | Tile grid profil/Explore memotong caption di tengah kata, mis. "Coba posting teks + foto, l…". | ✅ **Diperbaiki** — `excerpt()` kini memotong di batas kata + `overflow-wrap: break-word` di `.grid-text`. |
| **BUG-07** | Data seed aneh: bio Rani "Suka foto & kopi.fesf" (kemungkinan typo saat seed). | 📝 Data, bukan bug UI. Perbaiki di `database.sql` bila perlu. |
| **BUG-08** | Halaman login mencetak kredensial demo di bawah form (`admin@… / admin123`, `demo@… / demo123`). Praktis untuk demo, tetapi kredensial sebaiknya tidak tampil di halaman login. | 📝 Keputusan: dipertahankan untuk demo / dihapus sebelum pengumpulan. |
| **BUG-09** | Detail postingan tidak menampilkan jumlah like/komentar di samping ikon hati/komentar (di feed ada "1 suka"). | ✅ **Diperbaiki** — angka jumlah kini tampil di samping kedua ikon di `post.php`. |
| **BUG-10** | Halaman admin Komentar tidak punya kotak cari, padahal admin Postingan punya. | ✅ **Diperbaiki** — kotak cari (isi komentar / penulis / postingan) + tombol Reset ditambahkan. |

### 5.4 Observasi (bukan bug)

- **OBS-01:** Saat login sebagai admin, semua postingan di feed menampilkan
  tombol ✏️ Edit / 🗑️ Hapus, termasuk postingan pengguna lain. Ini **sengaja**
  sebagai moderasi admin (cek: `index.php` mengizinkan pemilik postingan
  **atau** admin). Sebagai demo, tombol itu tidak muncul di postingan orang lain.

---

## 6. Temuan QA yang Tidak Terbukti (Koreksi)

Temuan berikut dilaporkan QA tetapi **tidak cocok dengan kode** setelah
diperiksa langsung — dicatat di sini agar tidak dikerjakan ulang:

| Klaim QA | Hasil pengecekan kode |
|---|---|
| "Hapus komentar/postingan/user tanpa dialog konfirmasi di mana-mana" | **Tidak terbukti.** Semua tombol hapus memakai `onsubmit="return confirm(...)"` (`post.php`, `index.php`, `admin/posts.php`, `admin/comments.php`, `admin/users.php`). Kemungkinan dialog tidak terlihat karena alat QA otomatis menutup dialog `confirm()`. Yang benar-benar tanpa konfirmasi hanya ubah role (BUG-03, sudah diperbaiki). |
| "Teks tanpa spasi di mana-mana" (`ranimenyukai`, `3postingan`, `demoasfhjkl`, `Belum adaaktivitas.`) | **Tidak terbukti di kode terbaru.** Spasi ada di semua lokasi yang disebut (`activity.php`, `profile.php`, `.post-caption b { margin-right: 5px }`). Kemungkinan penyebab: browser penguji masih memakai CSS lama dari cache. **Tindakan:** hard refresh (`Ctrl+Shift+R`) lalu cek ulang. |
| "Pencarian postingan hanya cocok dari awal kata" | **Tertutup.** Setelah pull, pencarian "datang" dan "langitnya" (tengah kalimat) mengembalikan hasil. Pencarian "pagi" 0 hasil sudah benar karena caption "Selamat pagi semuanya!…" tidak lagi ada di database (lihat Bagian 7). |

---

## 7. Catatan Data Selama Pengujian

Selama jeda `git pull`, satu postingan demo ("Selamat pagi semuanya! Hari ini
semangat banget…", 1 like) hilang dari database: feed 5 → 4 postingan,
dashboard 4 like → 3 like. QA tidak menghapus postingan tersebut (yang dihapus
hanya postingan `[QA]` miliknya sendiri) dan akun `qatester` selamat, jadi
bukan reset database penuh. **Perlu dikonfirmasi:** apakah postingan itu
dihapus manual atau `database.sql` di-import ulang saat pull.

---

## 8. Batasan Pengujian

1. **Lebar HP 480px tidak dites secara murni.** Pengguna tidak sempat
   mengecilkan jendela; pengujian responsif dilakukan dengan zoom 300%
   (viewport CSS kira-kira selebar HP). Tidak ditemukan overflow, tetapi
   lebar CSS pastinya tidak terukur — perlakukan hasil responsif sebagai
   simulasi, bukan tes perangkat.
2. **Console browser tidak bisa dibuka** dari alat QA, sehingga error
   JavaScript tidak diperiksa langsung. Tidak ada error yang terlihat dari
   perilaku (semua fitur interaktif bekerja). Request gagal yang teramati:
   gambar rusak (BUG-02) dan redirect loop (BUG-01).
3. **Navigasi langsung ke URL `127.0.0.1`** ditolak alat QA
   (`invalid_request`); situs dibuka manual oleh pengguna dan semua navigasi
   dalam halaman bekerja. Satu langkah direct-URL (proteksi admin) dilakukan
   oleh pengguna dengan mengetik URL.
4. **Akses guest ke `/admin/*` (belum login) tidak dites.**

---

## 9. Riwayat Perbaikan Terkait Laporan Ini

| Commit | Perbaikan |
|---|---|
| `735f6ee4` | Toast mengambang (sukses hijau ✓ / gagal merah ⚠ / info biru ℹ, tombol ✕, hilang otomatis 5 detik); `flash()` bertipe + pesan tidak ditimpa (BUG-04) |
| `15119588` | Redirect `requireLogin()`/`requireAdmin()` absolut dari root (BUG-01) |
| `bf6bb5b6` | Pratinjau foto sebelum upload di composer & edit postingan (TC-29) |
| (commit perbaikan QA) | Konfirmasi ubah role, padding bawah 150px, jumlah like/komentar di detail postingan, kotak cari admin Komentar, `excerpt()` batas kata (BUG-03, BUG-05, BUG-06, BUG-09, BUG-10) |

---

## 10. Kesimpulan & Rekomendasi

1. **Tes ulang live setelah pull terakhir** untuk BUG-01 dan BUG-04
   (buka `/admin/index.php` sebagai demo; hapus satu postingan tes dan
   periksa toast-nya).
2. **Periksa file foto** `uploads/20261124_152543_c766f7f4c5cb.png` di laptop
   (BUG-02) — bila rusak, unggah ulang foto dan hapus postingan lamanya.
3. **Konfirmasi hilangnya postingan "Selamat pagi…"** (Bagian 7).
4. **Putuskan BUG-08** (kredensial di halaman login) sebelum pengumpulan.
5. **Tes responsif 480px murni** (DevTools device toolbar / HP asli) dan
   **akses guest ke `/admin/*`** sebagai pengujian susulan singkat.
6. Setelah poin 1–2 beres, aplikasi **layak diajukan** (ZIP proyek +
   link hosting) sesuai ketentuan pengumpulan.
