<?php
// ============================================================
// SosialKita — functions.php (helper)
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
        $stmt = $pdo->prepare('SELECT id, name, username, email, bio, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function isAdmin(): bool {
    $u = currentUser();
    return $u && $u['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['flash'] = 'Login dulu ya untuk lanjut.';
        header('Location: login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash'] = 'Halaman ini khusus admin.';
        header('Location: index.php');
        exit;
    }
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function flash(?string $msg = null): ?string {
    if ($msg !== null) {
        $_SESSION['flash'] = $msg;
        return null;
    }
    if (!empty($_SESSION['flash'])) {
        $m = $_SESSION['flash'];
        unset($_SESSION['flash']);
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

function excerpt(string $s, int $len): string {
    return strlen($s) > $len ? substr($s, 0, $len) . '…' : $s;
}
