# Panduan Deploy — Forum JMK48

Cara mempublikasikan Forum JMK48 ke hosting gratis (InfinityFree)
supaya bisa dibuka lewat link dan dikumpulkan ke Classroom.
Panduan ini untuk deploy pertama **dan** untuk update berikutnya.

## 1. Gambaran Umum

Deploy = 3 langkah:

1. **Upload file PHP** ke folder `htdocs` di hosting.
2. **Buat database MySQL** di hosting, lalu import tabelnya.
3. **Sesuaikan `config.php`** dengan kredensial database hosting.

Yang di laptop dan yang di hosting adalah **dua salinan yang berbeda**:
file di laptop tidak otomatis berubah kalau hosting di-update, begitu
juga sebaliknya.

## 2. Yang Dibutuhkan

| Kebutuhan | Keterangan |
|---|---|
| Akun InfinityFree | Gratis, tanpa kartu kredit — daftar di infinityfree.com |
| File proyek | `forumjmk48.zip` (isi folder proyek, tanpa `.git`) |
| File database hosting | `database-hosting.sql` (ada di dalam ZIP) |
| Browser | Chrome / Brave / Firefox |

> InfinityFree paket gratis: PHP 8.4 + MySQL, disk 5 GB, SSL gratis,
> subdomain gratis (`namasite.infinityfreeapp.com`). Batasan: maks
> 50.000 kunjungan/hari (lewat dari itu situs offline 24 jam),
> tanpa email/cron/SSH, dan akun bisa di-suspend kalau situs tidak
> dibuka sama sekali selama 30 hari (buka sesekali sebelum penilaian).

## 3. Deploy Pertama

### Langkah 1 — Buat hosting account

1. Login ke [dash.infinityfree.com](https://dash.infinityfree.com),
   buka **Create Account**.
2. Pilih **Subdomain** gratis, isi nama (mis. `forumjmk48`),
   domain `infinityfreeapp.com` → jadinya
   `https://forumjmk48.infinityfreeapp.com`.
3. Catat **username hosting** (mis. `if0_43542615`) dan password akun
   hosting — password ini juga dipakai sebagai password database.

### Langkah 2 — Buat database MySQL

1. Di panel hosting, buka **MySQL Databases** → **Create Database**,
   isi nama database (mis. `forumjmk48`).
2. Catat 4 hal ini (dibutuhkan di Langkah 4):
   - **MySQL Hostname** (mis. `sql101.infinityfree.com`)
   - **Database Name** (mis. `if0_43542615_forumjmk48`)
   - **MySQL Username** (mis. `if0_43542615`)
   - **MySQL Password** (= password akun hosting dari Langkah 1)

### Langkah 3 — Import tabel

1. Di panel hosting, buka **phpMyAdmin** untuk database yang baru dibuat.
2. Tab **Import** → pilih file **`database-hosting.sql`** → **Go**.
3. Harus muncul 4 tabel: `users`, `posts`, `comments`, `likes`
   (plus 3 akun demo dan 3 postingan contoh).

> ⚠️ Jangan import `database.sql` di hosting — file itu berisi
> `CREATE DATABASE` dan `USE` yang tidak diizinkan di InfinityFree,
> importnya gagal dengan error **#1044**. Selalu pakai
> `database-hosting.sql` untuk hosting. (Untuk instalasi di laptop,
> sebaliknya: pakai `database.sql` — lihat
> [PANDUAN-INSTALASI.md](PANDUAN-INSTALASI.md).)
>
> Peringatan **#1366** soal emoji (mis. ☕ di bio Rani) saat import
> boleh diabaikan — hanya emoji-nya yang hilang, tabel tetap jadi.

### Langkah 4 — Upload file ke `htdocs`

1. Di panel hosting, buka **File Manager** → masuk ke folder **`htdocs`**.
2. Upload `forumjmk48.zip` → klik kanan file → **Extract** (atau
   tombol **Upload & Unzip**), extract **di dalam `htdocs`**.
   Pastikan file seperti `index.php` dan `config.php` langsung ada
   di `htdocs`, bukan di dalam subfolder `forumjmk48/`.
3. Hapus file `forumjmk48.zip` dari `htdocs` setelah extract.
4. Alternatif: upload lewat FTP (FileZilla) ke folder `/htdocs`.

### Langkah 5 — Sesuaikan `config.php` di hosting

1. Di File Manager, buka (edit) file **`config.php`** yang ada di `htdocs`.
2. Ubah 4 baris ini dengan data dari Langkah 2:

```php
define('DB_HOST', 'sql101.infinityfree.com');   // MySQL Hostname
define('DB_NAME', 'if0_43542615_forumjmk48');   // Database Name
define('DB_USER', 'if0_43542615');              // MySQL Username
define('DB_PASS', 'isi-dengan-password-hosting');
```

3. Simpan. **Jangan pernah menimpa file ini dengan `config.php` dari
   laptop** — isinya beda (user/password database laptop ≠ hosting).

### Langkah 6 — Bersihkan & verifikasi

1. Hapus **`seed.php`** dari `htdocs` (file ini hanya untuk sekali
   pakai; `database-hosting.sql` sudah berisi akun demo).
2. Pastikan folder **`uploads/`** ada di `htdocs` beserta isinya
   (`.htaccess` dan `index.php` pelindung). Kalau belum ada, folder
   ini dibuat otomatis saat ada foto pertama di-upload.
3. Buka situs di browser: `https://namasite.infinityfreeapp.com`.
   Harusnya feed tampil.
4. Login dan tes: posting dari tombol **+**, like, komentar,
   edit profil, lalu login admin dan buka Dashboard Admin.

Akun demo (sama seperti di laptop):

| Peran | Email | Password |
|---|---|---|
| Admin | admin@forumjmk48.test | admin123 |
| User | demo@forumjmk48.test | demo123 |
| User | rani@forumjmk48.test | rani123 |

## 4. Deploy Ulang (Update)

Kalau ada perubahan kode setelah situs live, **jangan upload ulang
semuanya** — cukup file yang berubah:

1. Buat ZIP kecil berisi **hanya file yang berubah**
   (mis. `index.php`, `like.php`, `assets/css/style.css`).
2. Upload ZIP itu ke `htdocs` → extract di tempat.
   Kalau File Manager menanyakan "overwrite/timpa", jawab **YA**
   hanya untuk file-file di ZIP itu.
3. **JANGAN** memasukkan `config.php` ke dalam ZIP update.
   Kalau tidak sengaja ikut, jangan extract file itu.
4. Hapus ZIP dari `htdocs` setelah extract.
5. Verifikasi: buka situs, cek fitur yang diubah (mis. klik like —
   hati harus langsung merah tanpa reload). Jangan ubah data saat
   mengetes (jangan membuat postingan/komentar baru).

> Tips: sebelum update, catat dulu file apa saja yang berubah
> sejak deploy terakhir supaya ZIP update-nya tidak ada yang kurang.

## 5. Masalah Umum

| Gejala | Penyebab & Solusi |
|---|---|
| Import gagal **#1044 Access denied** | Salah file — import `database-hosting.sql`, bukan `database.sql` (lihat Langkah 3). |
| Warning **#1366 Incorrect string value** | Emoji di data (☕) tidak masuk. Aman diabaikan; perbaiki bio-nya lewat Edit Profil kalau mau. |
| "Database belum terhubung" di situs live | `config.php` di hosting salah / database belum di-import. Cek Langkah 2, 3, dan 5. |
| Halaman 404 / "Website not found" | File tidak ada di `htdocs` (mungkin ter-extract ke subfolder). Pindahkan file ke `htdocs` langsung. |
| Perubahan tidak muncul setelah update | Browser masih menampilkan cache lama. Hard refresh (`Ctrl+Shift+R`) atau tunggu sebentar; pastikan file di `htdocs` benar-benar ter-update (cek tanggal file di File Manager). |
| Situs tiba-tiba offline | Batas 50.000 kunjungan/hari terlampaui — situs aktif lagi setelah 24 jam. |
| Akun hosting di-suspend | Situs tidak dibuka selama 30 hari. Buka situs berkala sebelum penilaian; kalau terlanjur suspend, login ke panel InfinityFree dan ikuti petunjuk reaktivasi. |
| Foto upload gagal di live | Folder `uploads/` tidak bisa ditulis. Pastikan folder ada; `uploadImage()` membuatnya otomatis saat upload pertama. |

## 6. Checklist Sebelum Dikumpulkan

- [ ] Situs live bisa dibuka (HTTPS, tanpa error)
- [ ] Login admin + demo + rani semuanya bisa
- [ ] Posting (teks + foto), like, komentar, edit profil berfungsi
- [ ] Dashboard Admin tampil (statistik + kelola postingan/komentar/user)
- [ ] `seed.php` sudah dihapus dari `htdocs`
- [ ] `config.php` di hosting berisi kredensial hosting (bukan laptop)
- [ ] Link live + ZIP proyek + dokumen sudah ada di folder Drive pengumpulan
