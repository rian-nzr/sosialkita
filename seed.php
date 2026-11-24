<?php
// ============================================================
// SosialKita — seed.php
// JALANKAN SEKALI SAJA di browser, lalu HAPUS file ini.
// Membuat akun admin + demo dengan password yang benar.
// ============================================================
require_once __DIR__ . '/config.php';

$accounts = [
    ['Admin SosialKita', 'admin', 'admin@sosialkita.test', 'admin123', 'Akun admin — memantau semua postingan.', 'admin'],
    ['Demo User', 'demo', 'demo@sosialkita.test', 'demo123', 'Halo! Ini akun demo.', 'user'],
    ['Rani Putri', 'rani', 'rani@sosialkita.test', 'rani123', 'Suka foto & kopi ☕', 'user'],
];

$posts = [
    ['demo', 'Selamat datang di SosialKita! 🎉 Ini postingan pertama. Coba posting teks + foto, like, dan komentar ya.'],
    ['rani', 'Hari ini langitnya bagus banget. Semoga harimu juga cerah! ☀️'],
    ['demo', 'Tips hari ini: jangan lupa minum air putih dan istirahat yang cukup.'],
];

foreach ($accounts as [$name, $username, $email, $pass, $bio, $role]) {
    $stmt = $pdo->prepare('INSERT INTO users (name, username, email, password, bio, role)
        VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password), role=VALUES(role)');
    $stmt->execute([$name, $username, $email, password_hash($pass, PASSWORD_DEFAULT), $bio, $role]);
}

$userIds = [];
foreach ($pdo->query('SELECT id, username FROM users')->fetchAll() as $u) $userIds[$u['username']] = $u['id'];

foreach ($posts as [$username, $content]) {
    if (!isset($userIds[$username])) continue;
    $stmt = $pdo->prepare('INSERT INTO posts (user_id, content) VALUES (?,?)');
    $stmt->execute([$userIds[$username], $content]);
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Seed selesai</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<main class="container" style="max-width:560px;padding-top:60px">
  <div class="card" style="padding:28px;text-align:center">
    <h1>✅ Seed selesai</h1>
    <p>Akun yang bisa dipakai login:</p>
    <p><b>Admin:</b> admin@sosialkita.test / admin123<br>
       <b>User:</b> demo@sosialkita.test / demo123<br>
       <b>User:</b> rani@sosialkita.test / rani123</p>
    <p style="color:#dc2626"><b>Penting:</b> hapus file <code>seed.php</code> sekarang sebelum dikumpulkan / di-hosting.</p>
    <a class="btn btn-primary" href="login.php">Ke halaman login →</a>
  </div>
</main></body></html>
