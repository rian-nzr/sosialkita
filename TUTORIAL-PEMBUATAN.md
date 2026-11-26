# Tutorial Pembuatan — Forum JMK48 (Singkat)

Forum/media sosial sederhana berbasis **PHP native + MySQL** (tanpa framework),
dibuat sebagai tugas akhir. Pengguna bisa posting teks + foto, like, komentar,
dan admin bisa memantau semua postingan lewat dashboard.

## 1. Alat & Bahan

| Kebutuhan | Keterangan |
|---|---|
| PHP 8+ | `php -v` untuk cek versi |
| MySQL 8 / MariaDB | Database `forumjmk48` |
| Web server | Apache, atau `php -S localhost:8000` (paling gampang) |
| Browser | Chrome / Brave / Firefox |

## 2. Struktur Proyek

```
forumjmk48/
├── config.php          # Koneksi database + nama aplikasi
├── database.sql        # Struktur 4 tabel + 3 akun seed
├── seed.php            # Pembuat akun demo (jalankan sekali, lalu hapus)
├── index.php           # Feed + form posting
├── login.php / register.php / logout.php
├── post.php            # Detail postingan + komentar
├── edit_post.php / delete_post.php
├── edit_comment.php / delete_comment.php
├── like.php            # Like / unlike
├── profile.php / search.php / activity.php
├── admin/              # Dashboard + kelola postingan, komentar, pengguna
├── includes/           # functions.php, header.php, footer.php
├── assets/css/style.css
├── assets/js/          # Pratinjau foto + toast
└── uploads/            # Foto postingan (file .php diblokir .htaccess)
```

## 3. Database

`database.sql` membuat 4 tabel:

- **users** — nama, username, email, password (hash), bio, avatar (nama file foto profil), role (`user`/`admin`)
- **posts** — user_id, isi teks, nama file foto
- **comments** — post_id, user_id, isi komentar
- **likes** — post_id + user_id (unik, tidak bisa like 2x)

## 4. Langkah Pembuatan

1. **Buat database** — import `database.sql` lewat phpMyAdmin atau terminal:
   `mysql -u root -p < database.sql`
2. **Atur koneksi** — edit `config.php`: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. **(Opsional) Jalankan `seed.php` sekali** lewat browser untuk membuat akun
   demo, lalu **hapus file `seed.php`**.
4. **Jalankan server** — `php -S localhost:8000` dari dalam folder proyek,
   atau letakkan folder di `htdocs` (XAMPP/LAMPP) lalu buka
   `http://localhost/forumjmk48/`.
5. **Bangun fitur** (sudah jadi di proyek ini):
   - Autentikasi: register/login/logout, password di-hash (`password_hash`),
     semua query pakai PDO prepared statement (aman dari SQL injection).
   - Posting: teks + foto (JPG/PNG/GIF/WebP, maks 2 MB, nama file acak),
     pratinjau foto sebelum upload, edit/hapus postingan sendiri.
   - Interaksi: like/unlike, komentar + edit/hapus komentar.
   - Profil: nama, bio & foto profil bisa diedit (pratinjau sebelum simpan), grid postingan.
   - Pencarian & Explore: cari pengguna/postingan, halaman Jelajahi.
   - Aktivitas: daftar like & komentar di postingan sendiri.
   - Admin: dashboard statistik + kelola postingan, komentar, pengguna
     (ubah role, hapus user).
   - Tampilan: mode gelap, bottom nav pill, toast notifikasi.

## 5. Akun Demo

| Peran | Email | Password |
|---|---|---|
| Admin | admin@forumjmk48.test | admin123 |
| User | demo@forumjmk48.test | demo123 |
| User | rani@forumjmk48.test | rani123 |

## 6. Hosting (Pengumpulan)

Upload isi folder ke `htdocs` hosting (mis. InfinityFree — gratis),
buat database MySQL di panel hosting, import `database.sql`,
sesuaikan `config.php`, hapus `seed.php`. Selesai.
