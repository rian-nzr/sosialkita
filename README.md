# 💬 SosialKita — Website Sosial Media (PHP + MySQL)

Proyek akhir: website sosial media / forum sederhana.
User bisa posting teks + foto, like, dan komentar. Admin memantau semuanya lewat dashboard.

**GitHub:** https://github.com/rian-nzr/sosialkita

**Tampilan:** gaya Instagram — feed sempit 470px di tengah, baris Stories
(lingkaran avatar ber-ring gradien), kartu postingan dengan foto persegi 1:1,
ikon hati/komentar/bagikan, jumlah suka, caption, dan kolom komentar di bawah;
postingan teks tampil sebagai kartu gradien; profil menampilkan grid foto 3 kolom
ala IG; halaman login/daftar berupa kotak putih 350px ala IG.

## ✨ Fitur

**User (biasa)**
- Daftar & login (password di-hash `password_hash`, session)
- Posting teks + foto (JPG/PNG/GIF/WebP, maks 2 MB) — Create
- Lihat feed semua postingan (terbaru dulu) — Read
- Edit & hapus postingan sendiri — Update & Delete
- Like / unlike postingan
- Komentar di postingan, edit & hapus komentar sendiri
- Profil user + edit bio

**Admin**
- Dashboard: total pengguna, postingan, komentar, like + postingan & pengguna terbaru
- Kelola Postingan: cari, lihat, hapus postingan siapa pun
- Kelola Komentar: lihat & hapus komentar siapa pun
- Kelola Pengguna: ubah role user ↔ admin, hapus pengguna (postingan ikut terhapus)

**Keamanan dasar**
- Prepared statement (PDO) — anti SQL injection
- `htmlspecialchars` di semua output — anti XSS
- Validasi tipe & ukuran file upload, nama file acak
- Akses admin dicek di setiap halaman admin (`requireAdmin()`)

## 🗂️ Struktur folder

```
sosialkita/
├── config.php            # koneksi database (UBAH user/pass di sini)
├── database.sql          # skema database — import dulu
├── seed.php              # JALANKAN SEKALI, lalu HAPUS — bikin akun demo
├── index.php             # beranda / feed + form buat postingan
├── login.php  register.php  logout.php
├── post.php              # detail postingan + komentar
├── edit_post.php  delete_post.php
├── edit_comment.php  delete_comment.php
├── like.php              # toggle like
├── profile.php
├── admin/
│   ├── index.php         # dashboard admin
│   ├── posts.php         # kelola postingan
│   ├── comments.php      # kelola komentar
│   └── users.php         # kelola pengguna
├── includes/
│   ├── functions.php     # helper: auth, upload, timeAgo, dll
│   ├── header.php  footer.php
├── assets/css/style.css  # semua styling (tanpa framework)
└── uploads/              # foto postingan (dibuat otomatis)
```

## 🚀 Cara jalanin di XAMPP (lokal)

1. Copy folder `sosialkita` ke `C:\xampp\htdocs\` (Windows) atau `/Applications/XAMPP/htdocs/` (Mac).
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin` → tab **Import** → pilih `database.sql` → **Go**.
   Database `sosialkita` + 4 tabel (`users`, `posts`, `comments`, `likes`) akan dibuat.
4. Buka `http://localhost/sosialkita/seed.php` **sekali** untuk membuat akun demo, lalu **hapus file `seed.php`**.
5. Buka `http://localhost/sosialkita/` — selesai.

> Kalau MySQL kamu pakai password (bukan root kosong), ubah `DB_USER` / `DB_PASS` di `config.php`.

## 🔑 Akun demo (dari seed.php)

| Role  | Email | Password |
|-------|-------|----------|
| Admin | admin@sosialkita.test | admin123 |
| User  | demo@sosialkita.test | demo123 |
| User  | rani@sosialkita.test | rani123 |

## 🗄️ Database

- `users` — id, name, username, email, password (hash), bio, role (`user`/`admin`), created_at
- `posts` — id, user_id (FK → users, CASCADE), content, image, created_at, updated_at
- `comments` — id, post_id (FK → posts, CASCADE), user_id (FK → users, CASCADE), content, created_at, updated_at
- `likes` — id, post_id, user_id, UNIQUE(post_id, user_id) — 1 user cuma bisa like 1x per post

## 🌐 Cara hosting (gratis, buat link Classroom)

Opsi paling gampang untuk PHP + MySQL:

1. **InfinityFree** (infinityfree.com) — gratis, ada MySQL + PHP.
   Daftar → buat domain `namamu.infinityfreeapp.com` → buka **File Manager**, upload isi folder `sosialkita` ke `htdocs/` → buka **MySQL Databases**, buat database → buka **phpMyAdmin**, import `database.sql` → ubah `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` di `config.php` sesuai yang dikasih InfinityFree → jalankan `namadomainmu/seed.php` sekali, lalu hapus `seed.php`.
2. **000webhost / ByetHost / AwardSpace** — langkahnya sama: upload file, buat DB, import SQL, sesuaikan config.
3. Jangan lupa: hapus `seed.php` setelah dipakai, dan jangan upload file `.zip` berisi password asli.

## 📦 Yang dikumpulkan ke Classroom

- File **ZIP** dari folder proyek ini (tanpa folder `uploads/` yang berisi foto demo — boleh ikut, tidak wajib).
- **Link website** yang sudah di-hosting.

## 👥 Pembagian tugas (saran buat kelompok 4–5 orang)

| Anggota | Bagian |
|---------|--------|
| 1 | Auth (register/login) + database |
| 2 | CRUD postingan + upload foto |
| 3 | Komentar + like |
| 4 | Dashboard admin |
| 5 | Desain CSS + README + hosting & presentasi |
