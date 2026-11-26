<?php
// ============================================================
// Forum JMK48 — config.php
// UBAH bagian ini sesuai setting XAMPP / hosting kamu.
// Di XAMPP default: host=localhost, user=root, password='' (kosong)
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'forumjmk48');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_NAME', 'Forum JMK48');
define('BASE_URL', ''); // kosongkan = otomatis. Isi misal '/forumjmk48' kalau perlu.
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

session_start();

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Pesan ramah kalau database belum di-import.
    die('
    <div style="font-family:sans-serif;max-width:560px;margin:60px auto;line-height:1.6">
      <h2>Database belum terhubung 😅</h2>
      <p>Import dulu file <b>database.sql</b> ke phpMyAdmin (database <b>forumjmk48</b>), lalu jalankan <b>seed.php</b> sekali untuk membuat akun admin &amp; demo.</p>
      <p style="color:#666;font-size:14px">Detail error: ' . htmlspecialchars($e->getMessage()) . '</p>
    </div>');
}

require_once __DIR__ . '/includes/functions.php';
