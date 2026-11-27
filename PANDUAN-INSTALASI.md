# Panduan Instalasi di Laptop — Forum JMK48

Cara memasang Forum JMK48 di laptop sendiri (Linux / Windows / Mac)
supaya bisa dibuka dan dites tanpa hosting. Cocok untuk demo ke dosen
atau untuk mengerjakan tugas secara berkelompok.

## 1. Yang Dibutuhkan

| Kebutuhan | Keterangan |
|---|---|
| PHP 8+ | Cek dengan `php -v` di terminal |
| MySQL 8 / MariaDB | Harus sudah terpasang dan berjalan |
| Git | Untuk mengambil kode dari GitHub (opsional, bisa pakai ZIP) |
| Browser | Chrome / Brave / Firefox |

> Belum ada PHP atau MySQL? Di Ubuntu/Debian: `sudo apt install php php-mysql mysql-server git`.
> Di Windows, paling gampang pakai **XAMPP** (sudah berisi PHP + MySQL + Apache).

## 2. Ambil Kodenya

**Cara A — lewat Git (disarankan, gampang update):**

```bash
git clone https://github.com/rian-nzr/sosialkita.git forumjmk48
cd forumjmk48
```

**Cara B — lewat ZIP:**

Ekstrak file `forumjmk48.zip`, lalu buka terminal di dalam folder hasil
ekstrak (folder `forumjmk48`).

## 3. Buat Database + Import

Masuk ke MySQL, buat database `forumjmk48`, lalu import `database.sql`.
File ini sudah berisi 4 tabel **plus 3 akun demo dan 3 postingan contoh**,
jadi tidak perlu menjalankan `seed.php` lagi.

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS forumjmk48;"
mysql -u root -p forumjmk48 < database.sql
```

(Masukkan password MySQL saat diminta. Kalau MySQL di laptopmu tanpa
password, hapus `-p` dari perintah di atas.)

Cek hasilnya — harus ada 4 tabel:

```bash
mysql -u root -p forumjmk48 -e "SHOW TABLES;"
```

## 4. Atur Koneksi Database (`config.php`)

Buka file `config.php`, sesuaikan 4 baris ini dengan MySQL di laptopmu:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'forumjmk48');
define('DB_USER', 'root');
define('DB_PASS', '');
```

- `DB_USER` / `DB_PASS` — isi dengan user & password MySQL kamu.
  Di XAMPP default: user `root`, password kosong (`''`).
- Kalau di laptopmu login root MySQL pakai password, isi `DB_PASS`
  dengan password itu. Kalau tetap gagal, buat user khusus:

```bash
mysql -u root -p -e "CREATE USER 'forum'@'localhost' IDENTIFIED BY 'forum123'; GRANT ALL ON forumjmk48.* TO 'forum'@'localhost'; FLUSH PRIVILEGES;"
```

lalu isi `config.php` dengan `DB_USER` = `forum`, `DB_PASS` = `forum123`.

## 5. Jalankan Server

Dari dalam folder proyek, jalankan server bawaan PHP:

```bash
php -S 127.0.0.1:8000
```

Lalu buka di browser:

```
http://127.0.0.1:8000/
```

Biarkan terminal tetap terbuka selama memakai situs. Untuk berhenti,
tekan `Ctrl + C` di terminal.

> Alternatif (XAMPP/LAMPP): salin folder proyek ke `htdocs`, lalu buka
> `http://localhost/forumjmk48/`.

## 6. Login & Coba

| Peran | Email | Password |
|---|---|---|
| Admin | admin@forumjmk48.test | admin123 |
| User | demo@forumjmk48.test | demo123 |
| User | rani@forumjmk48.test | rani123 |

Coba: posting dari tombol **+** (halaman Buat postingan), like
(klik hati atau double-tap foto), komentar, edit profil, lalu buka
Dashboard Admin sebagai admin.

## 7. Update Kode (Kalau Ada Perubahan Baru)

Kalau mengambil kode lewat Git, update-nya cukup:

```bash
cd forumjmk48
git pull
```

Lalu refresh browser. **Database tidak ikut berubah** dengan `git pull` —
kalau ada perubahan struktur tabel, import ulang `database.sql`
(hati-hati: import ulang menghapus data lama, jadi backup dulu kalau
ada postingan yang mau disimpan).

Kalau mengambil kode lewat ZIP, ekstrak ulang ZIP terbaru menimpa
folder lama — **jangan timpa `config.php`** kalau sudah disesuaikan
dengan MySQL laptopmu.

## 8. Masalah Umum

| Gejala | Penyebab & Solusi |
|---|---|
| "Database belum terhubung" | Database `forumjmk48` belum dibuat / belum di-import. Ulangi Langkah 3. Perintahnya harus jalan tanpa error. |
| "Access denied for user 'root'" | User/password di `config.php` salah. Sesuaikan dengan Langkah 4. |
| "Could not connect to MySQL" / halaman tidak bisa dibuka | MySQL belum jalan. Ubuntu/Debian: `sudo service mysql start` (atau `sudo service mariadb start`). XAMPP: klik Start pada MySQL di Control Panel. |
| "Address already in use" / port 8000 dipakai | Ada server lain di port 8000. Pakai port lain: `php -S 127.0.0.1:8080`, lalu buka `http://127.0.0.1:8080/`. |
| `php: command not found` | PHP belum terpasang. Instal dulu (lihat Langkah 1). |
| Foto postingan tidak muncul / gagal upload | Cek folder `uploads/` ada dan bisa ditulis. Linux: `chmod 755 uploads` (atau `777` kalau masih gagal). Foto maksimal 2 MB, format JPG/PNG/GIF/WebP. |
| Halaman putih / error 500 | Cek sintaks file yang terakhir diubah: `php -l namafile.php`. Lihat juga error PHP di terminal tempat server berjalan. |
| Login demo gagal ("Email atau password salah") | Password yang benar: `demo123` (untuk demo), `admin123` (admin), `rani123` (rani). Kalau database di-import ulang, akun kembali ke bawaan ini. |

## 9. Catatan Penting

- Instalasi di laptop **terpisah** dari situs live di InfinityFree
  (`https://forumjmk48.infinityfreeapp.com`). Yang diubah di laptop
  tidak otomatis muncul di situs live, dan sebaliknya.
- File `config.php` di laptop dan di hosting **isinya beda**
  (user/password database berbeda). Jangan salin `config.php` dari
  laptop ke hosting, atau sebaliknya.
- File `seed.php` tidak perlu dijalankan — `database.sql` sudah berisi
  akun demo. Kalau `seed.php` masih ada di folder proyek, hapus saja
  sebelum dikumpulkan.
- Sebelum pengumpulan, jalankan [PANDUAN-PENGUJIAN.md](PANDUAN-PENGUJIAN.md)
  untuk mengetes semua fitur.
