<?php
// ============================================================
// Forum JMK48 — functions.php (helper)
// ============================================================

function e($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    global $pdo;
    if (!isLoggedIn()) return null;
    static $user = null;
    if ($user === null) {
        $stmt = $pdo->prepare('SELECT id, name, username, email, bio, avatar, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function isAdmin(): bool {
    $u = currentUser();
    return $u && $u['role'] === 'admin';
}

// Redirect di sini WAJIB pakai path absolut dari root web ('/index.php',
// bukan 'index.php'): 'Location: index.php' yang relatif akan diartikan
// browser relatif terhadap URL saat ini — dari /admin/ itu berarti
// /admin/index.php, sehingga requireAdmin() mengulang redirect ke dirinya
// sendiri sampai browser menampilkan ERR_TOO_MANY_REDIRECTS.
function requireLogin(): void {
    if (!isLoggedIn()) {
        flash('Login dulu ya untuk lanjut.', 'error');
        header('Location: /login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        flash('Halaman ini khusus admin.', 'error');
        header('Location: /index.php');
        exit;
    }
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Pesan kilat (toast): flash('...') = sukses, flash('...', 'error') = gagal,
// flash('...', 'info') = info. Pesan yang sudah menunggu TIDAK ditimpa oleh
// pesan berikutnya — jadi pesan sukses (mis. "Postingan berhasil dihapus.")
// tidak hilang ketimpa pesan dari halaman tujuan redirect.
function flash(?string $msg = null, string $type = 'success'): ?array {
    if ($msg !== null) {
        if (empty($_SESSION['flash'])) {
            $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        }
        return null;
    }
    if (!empty($_SESSION['flash'])) {
        $m = $_SESSION['flash'];
        unset($_SESSION['flash']);
        // Kompatibel dengan sesi lama yang menyimpan string biasa.
        if (is_string($m)) $m = ['msg' => $m, 'type' => 'info'];
        return $m;
    }
    return null;
}

function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'baru saja';
    if ($diff < 3600) return floor($diff / 60) . ' mnt lalu';
    if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
    if ($diff < 86400 * 7) return floor($diff / 86400) . ' hari lalu';
    return date('d M Y, H:i', $time);
}

/** Upload foto postingan. Return nama file atau null. $error diisi kalau gagal. */
function uploadImage(array $file, ?string &$error = null): ?string {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) { $error = 'Upload gagal, coba lagi.'; return null; }
    if ($file['size'] > MAX_UPLOAD_SIZE) { $error = 'Foto maksimal 2 MB.'; return null; }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_TYPES, true)) { $error = 'Format foto harus JPG, PNG, GIF, atau WebP.'; return null; }

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) { $error = 'File yang diupload bukan gambar.'; return null; }

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        $error = 'Gagal menyimpan foto.';
        return null;
    }
    return $name;
}

function deleteImage(?string $filename): void {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        unlink(UPLOAD_DIR . $filename);
    }
}

function postCounts(int $postId): array {
    global $pdo;
    $c = $pdo->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ?');
    $c->execute([$postId]);
    $l = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE post_id = ?');
    $l->execute([$postId]);
    return ['comments' => (int) $c->fetchColumn(), 'likes' => (int) $l->fetchColumn()];
}

function userLiked(int $postId, int $userId): bool {
    global $pdo;
    $s = $pdo->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
    $s->execute([$postId, $userId]);
    return (bool) $s->fetchColumn();
}

// Potong teks di batas kata (bukan di tengah kata) + '…'.
// Pakai mb_* kalau tersedia supaya karakter UTF-8 tidak terpotong.
function excerpt(string $s, int $len): string {
    $strlen = function_exists('mb_strlen') ? mb_strlen($s) : strlen($s);
    if ($strlen <= $len) return $s;
    $substr = function_exists('mb_substr')
        ? function ($str, $start, $l) { return mb_substr($str, $start, $l); }
        : function ($str, $start, $l) { return substr($str, $start, $l); };
    $cut = $substr($s, 0, $len);
    $strrpos = function_exists('mb_strrpos') ? 'mb_strrpos' : 'strrpos';
    $sp = $strrpos($cut, ' ');
    if ($sp !== false && $sp > $len * 0.5) $cut = $substr($cut, 0, $sp);
    return $cut . '…';
}

// Avatar pengguna: foto profil kalau ada, kalau belum diupload tampil
// huruf pertama nama di lingkaran gradien (perilaku lama).
// $extra = kelas tambahan, mis. 'lg' untuk avatar besar di halaman profil.
// $base adalah '../' di halaman /admin/ (lihat header.php), '' di root.
function avatarHtml(array $u, string $extra = ''): string {
    $base = $GLOBALS['base'] ?? '';
    $cls = trim('avatar ' . $extra);
    $name = (string) ($u['name'] ?? '?');
    if (!empty($u['avatar'])) {
        return '<div class="' . e($cls) . '"><img src="' . $base . 'uploads/' . e($u['avatar']) . '" alt="Foto ' . e($name) . '"></div>';
    }
    $initial = function_exists('mb_substr') ? mb_substr($name, 0, 1, 'UTF-8') : substr($name, 0, 1);
    return '<div class="' . e($cls) . '">' . e(strtoupper($initial)) . '</div>';
}
