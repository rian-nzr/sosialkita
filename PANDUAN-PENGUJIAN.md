# Panduan Pengujian Sistem — Forum JMK48 (Singkat)

Cara mengetes Forum JMK48 sebelum dikumpulkan. Ikuti sesuai urutan —
tiap baris: lakukan langkahnya, bandingkan dengan hasil yang diharapkan.

## 1. Persiapan

1. Jalankan server: `php -S localhost:8000` dari dalam folder proyek
   (atau buka link hosting kalau sudah di-deploy).
2. Buka `http://localhost:8000/` di browser.
3. Siapkan akun tes:

| Peran | Email | Password |
|---|---|---|
| Admin | admin@forumjmk48.test | admin123 |
| User | demo@forumjmk48.test | demo123 |
| User | rani@forumjmk48.test | rani123 |

## 2. Daftar Tes

| No | Fitur | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| 1 | Feed (guest) | Buka beranda tanpa login | Feed tampil, bisa lihat postingan |
| 2 | Register | Daftar akun baru | Berhasil + langsung login |
| 3 | Login benar | Login sebagai demo | Masuk ke beranda + toast sambutan |
| 4 | Login salah | Password salah | Pesan "Email atau password salah." |
| 5 | Posting teks | Tulis teks → Kirim | Postingan muncul di puncak feed |
| 6 | Posting foto | Pilih foto → lihat pratinjau → Kirim | Pratinjau tampil, foto ikut tayang |
| 7 | Like | Klik hati, lalu klik lagi | Like bertambah lalu berkurang |
| 8 | Komentar | Tulis komentar → Kirim | Komentar muncul + hitungan naik |
| 9 | Edit komentar | Edit komentar sendiri | Tersimpan + label "diedit" |
| 10 | Hapus komentar | Hapus komentar sendiri | Komentar hilang + toast sukses |
| 11 | Edit postingan | Edit postingan sendiri | Tersimpan + label "diedit" |
| 12 | Hapus postingan | Hapus postingan dari detailnya | Postingan hilang + toast "Postingan berhasil dihapus." |
| 13 | Profil | Buka profil sendiri → ubah nama, bio, dan foto profil | Tersimpan + toast "Profil berhasil disimpan."; foto muncul di semua tempat (feed, komentar, dashboard) |
| 14 | Profil orang | Buka profil rani | Tampil bio + postingan, tanpa tombol edit |
| 15 | Cari pengguna | Cari "rani" | 1 pengguna ditemukan |
| 16 | Cari postingan | Cari kata di tengah caption | Postingan ditemukan (bukan cuma awal kata) |
| 17 | Explore | Buka Cari tanpa kata kunci | Muncul "Disarankan untuk Anda" + grid "Jelajahi" |
| 18 | Aktivitas | Buka menu hati (Activity) | Daftar like & komentar di postinganmu |
| 19 | Logout | Klik logout | Kembali ke halaman login |
| 20 | Admin login | Login sebagai admin | Langsung ke Dashboard Admin |
| 21 | Dashboard | Buka Dashboard Admin | Statistik pengguna/postingan/komentar/like tampil |
| 22 | Admin Postingan | Buka Kelola Postingan + cari | Tabel + kotak cari bekerja |
| 23 | Admin Komentar | Buka Kelola Komentar + cari | Tabel + kotak cari bekerja |
| 24 | Ubah role | Admin → Jadikan admin (user demo) | **Muncul dialog konfirmasi dulu** (jangan klik OK kalau cuma ngetes) |
| 25 | Proteksi admin | Login sebagai demo → buka `/admin/index.php` | Dialihkan ke beranda + toast merah "Halaman ini khusus admin." |
| 26 | Guest ke admin | Logout → buka `/admin/index.php` | Dialihkan ke halaman login |
| 27 | Mode gelap | Jelajahi semua halaman | Semua halaman gelap, teks terbaca |
| 28 | Responsif | Kecilkan jendela ke ~480px | Tidak ada teks kepotong / tombol ketutup / scroll samping |
| 29 | Hapus foto profil | Buka profil sendiri → centang "Hapus foto profil" → Simpan | Avatar kembali ke huruf awal; file foto lama terhapus |

## 3. Cara Melaporkan Temuan

Kalau ada langkah yang hasilnya beda dari kolom terakhir, catat:

1. **Nomor tes** (mis. No. 12)
2. **Yang terjadi** (mis. toast berbunyi "Postingan tidak ditemukan.")
3. **Screenshot** halaman tersebut

## 4. Bersihkan Data Tes

Setelah selesai mengetes, hapus postingan/komentar/akun yang dibuat
khusus untuk tes (beri awalan `[TES]` saat membuat data tes supaya
gampang dicari), dan kembalikan bio akun demo kalau sempat diubah.
