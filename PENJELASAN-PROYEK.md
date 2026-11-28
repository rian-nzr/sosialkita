# Penjelasan Proyek — Forum JMK48

Dokumen ini menjelaskan proyek **Forum JMK48** secara menyeluruh —
latar belakang, fitur, teknologi, cara kerja, dan hasil pengujian —
untuk dipresentasikan kepada dosen pengampu.

## 1. Identitas Proyek

| | |
|---|---|
| **Nama proyek** | Forum JMK48 |
| **Jenis** | Website media sosial / forum (tugas akhir) |
| **Teknologi** | PHP native + MySQL (tanpa framework) |
| **Situs live** | https://forumjmk48.infinityfreeapp.com |
| **Kode sumber** | https://github.com/rian-nzr/sosialkita |
| **Kelompok** | 4–5 orang (lihat Bagian 11) |

## 2. Latar Belakang & Tujuan

Media sosial adalah tempat orang berbagi cerita, foto, dan berinteraksi
lewat like dan komentar. Proyek ini membangun media sosial sederhana
dari nol — tanpa framework dan tanpa CMS — supaya setiap bagian
(autentikasi, posting, interaksi, sampai panel admin) dipahami dan
dikerjakan sendiri oleh kelompok.

**Tujuan proyek:**

1. Membangun website media sosial yang berfungsi penuh: daftar, login,
   posting teks + foto, like, komentar, dan profil.
2. Menerapkan keamanan dasar web: password di-hash, query memakai
   prepared statement (aman dari SQL injection), dan output di-escape
   (aman dari XSS).
3. Menyediakan panel admin untuk memantau dan mengelola seluruh konten
   dan pengguna.
4. Mendeploy proyek ke hosting gratis supaya bisa dibuka dan dinilai
   lewat link, tidak hanya di laptop.

## 3. Fitur Utama

### Untuk semua pengunjung (tamu)

- Melihat feed postingan dan halaman detail postingan.
- Mencari pengguna dan postingan, serta menjelajahi postingan populer
  di halaman Explore.

### Untuk pengguna terdaftar

- **Posting** — lewat halaman khusus Buat Postingan (tombol **+** di
  navigasi bawah): teks sampai 2000 karakter + foto (JPG/PNG/GIF/WebP,
  maks 2 MB) dengan pratinjau foto sebelum diunggah.
- **Like** — klik ikon hati, atau **double-tap foto/teks postingan**
  ala Instagram (dengan animasi hati besar). Like langsung tersimpan
  tanpa me-reload halaman, dan posisi scroll tetap di postingan itu.
- **Komentar** — tulis, edit, dan hapus komentar sendiri.
- **Profil** — ubah nama, bio, dan foto profil (form muncul saat tombol
  "✏️ Edit Profil" ditekan; foto profil tampil bulat); grid postingan
  sendiri ala Instagram.
- **Aktivitas** — daftar like & komentar terbaru di postingan sendiri.

### Untuk admin

- **Dashboard** — statistik jumlah pengguna, postingan, komentar, dan like.
- **Kelola Postingan** — lihat, cari, dan hapus postingan siapa pun.
- **Kelola Komentar** — lihat, cari, dan hapus komentar siapa pun.
- **Kelola Pengguna** — cari pengguna, ubah role (user ↔ admin, dengan
  dialog konfirmasi), dan hapus pengguna. Admin tidak bisa menghapus
  atau menurunkan role dirinya sendiri.

## 4. Teknologi yang Digunakan

| Lapisan | Teknologi | Alasan memilih |
|---|---|---|
| Bahasa | PHP 8 (native, tanpa framework) | Mudah dipelajari, cocok untuk tugas akhir |
| Database | MySQL (MariaDB di pengembangan) | Relasional, cocok untuk data pengguna–postingan |
| Frontend | HTML + CSS + JavaScript (tanpa library) | Ringan, tidak ada dependensi |
| Server lokal | PHP built-in server (`php -S`) | Tidak perlu instalasi server tambahan |
| Hosting | InfinityFree (gratis) | PHP + MySQL gratis, ada SSL |
| Version control | Git + GitHub | Riwayat perubahan dan kerja kelompok |

## 5. Peran Pengguna

| Peran | Hak akses |
|---|---|
| **Tamu** | Melihat feed, detail postingan, pencarian, Explore |
| **User** | Semua hak tamu + posting, like, komentar, edit profil |
| **Admin** | Semua hak user + dashboard dan pengelolaan konten & pengguna |

## 6. Struktur Database

Empat tabel, saling terhubung lewat foreign key:

- **`users`** — id, name, username (unik), email (unik), password
  (hash `password_hash`), bio, avatar (nama file foto profil),
  role (`user`/`admin`), created_at.
- **`posts`** — id, user_id, content (teks), image (nama file foto),
  created_at, updated_at.
- **`comments`** — id, post_id, user_id, content, created_at, updated_at.
- **`likes`** — id, post_id, user_id, created_at. Kombinasi
  post_id + user_id bersifat unik, jadi satu pengguna tidak bisa
  me-like satu postingan dua kali.

Relasi: satu user punya banyak postingan, komentar, dan like;
satu postingan punya banyak komentar dan like.

## 7. Alur Penggunaan

**Alur pengguna biasa:**

```
Daftar → Login → Lihat feed → Buat postingan (tombol +)
→ Like / komentar postingan orang → Edit profil → Logout
```

**Alur admin:**

```
Login sebagai admin → Dashboard (lihat statistik)
→ Kelola Postingan / Komentar / Pengguna → Ubah role / hapus konten
```

**Alur like (contoh alur teknis):**

```
Klik hati → JavaScript mengirim POST ke like.php (AJAX)
→ Server toggle like di tabel likes → membalas JSON {liked, count}
→ Hati langsung merah + jumlah "suka" berubah, halaman tidak reload
```

## 8. Struktur Folder Proyek

```
forumjmk48/
├── config.php            # Koneksi database + nama aplikasi
├── database.sql          # Skema + data awal (untuk laptop)
├── database-hosting.sql  # Skema + data awal (untuk hosting)
├── index.php             # Beranda / feed
├── create.php            # Halaman buat postingan
├── post.php              # Detail postingan + komentar
├── like.php              # Toggle like (mendukung AJAX)
├── profile.php           # Profil + edit profil
├── search.php            # Pencarian + Explore
├── activity.php          # Aktivitas (like & komentar)
├── login.php / register.php / logout.php
├── edit_post.php / delete_post.php
├── edit_comment.php / delete_comment.php
├── admin/                # Dashboard + kelola konten & pengguna
├── includes/             # functions.php, header.php, footer.php
├── assets/css/style.css  # Tampilan mode gelap
├── assets/js/            # Pratinjau foto, toast, like AJAX
└── uploads/              # Foto postingan (file .php diblokir)
```

## 9. Keamanan

- **Password** disimpan sebagai hash (`password_hash`), bukan teks biasa.
- **Semua query** memakai PDO prepared statement — aman dari
  SQL injection.
- **Semua output** dari pengguna di-escape — aman dari XSS.
- **Upload foto** divalidasi: tipe file (gambar saja), ukuran maks 2 MB,
  nama file diacak, dan folder `uploads/` memblokir eksekusi file `.php`.
- **Halaman admin** hanya bisa dibuka oleh akun ber-role admin;
  pengguna biasa dialihkan ke beranda, tamu dialihkan ke login.
- **Aksi penting** (hapus postingan/komentar, ubah role) memakai
  dialog konfirmasi.

## 10. Pengujian

Pengujian dilakukan dengan 30 skenario pada
[PANDUAN-PENGUJIAN.md](PANDUAN-PENGUJIAN.md), mencakup: feed tamu,
register, login benar/salah, posting teks & foto, like (termasuk
double-tap), komentar, edit/hapus postingan & komentar, edit profil
(nama/bio/foto), pencarian, Explore, aktivitas, logout, seluruh
halaman admin, proteksi admin, mode gelap, dan tampilan responsif.

Hasil pengujian dicatat pada `LAPORAN-QA.md` — termasuk 10 bug yang
ditemukan dan diperbaiki selama pengujian (mis. redirect admin yang
looping, toast yang menyesatkan setelah hapus postingan, dan tombol
like yang me-reload halaman — kini sudah AJAX).

## 11. Anggota Kelompok

| No | Nama | NIM | Peran dalam proyek |
|---|---|---|---|
| 1 | [Nama anggota 1] | [NIM] | [mis. Ketua / backend] |
| 2 | [Nama anggota 2] | [NIM] | [mis. Frontend] |
| 3 | [Nama anggota 3] | [NIM] | [mis. Database] |
| 4 | [Nama anggota 4] | [NIM] | [mis. Pengujian & dokumentasi] |
| 5 | [Nama anggota 5] | [NIM] | [mis. Deploy & presentasi] |

> Lengkapi tabel di atas sebelum presentasi (sesuaikan jumlah anggota
> dengan kelompok sebenarnya; hapus baris yang tidak dipakai).

## 12. Cara Mencoba (untuk Dosen)

1. Buka situs live: https://forumjmk48.infinityfreeapp.com
2. Login dengan salah satu akun demo:

| Peran | Email | Password |
|---|---|---|
| Admin | admin@forumjmk48.test | admin123 |
| User | demo@forumjmk48.test | demo123 |
| User | rani@forumjmk48.test | rani123 |

3. Coba: buat postingan dari tombol **+**, like dengan klik hati
   atau double-tap foto, beri komentar, lalu buka Dashboard Admin
   sebagai admin.

## 13. Pengembangan ke Depan

Fitur yang bisa ditambahkan setelah tugas ini:

- Notifikasi real-time saat postingan di-like/dikomentari.
- Follow antar pengguna dan feed khusus dari yang di-follow.
- Edit foto langsung di browser (crop/rotate) sebelum upload.
- Pencarian dengan filter dan pagination pada feed.
