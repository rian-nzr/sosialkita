<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Dashboard Admin';
$user = currentUser();

$stats = [
    'Pengguna'  => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'Postingan' => (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn(),
    'Komentar'  => (int) $pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn(),
    'Like'      => (int) $pdo->query('SELECT COUNT(*) FROM likes')->fetchColumn(),
];
$recentPosts = $pdo->query('
    SELECT p.*, u.name, u.username,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
    FROM posts p JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC LIMIT 5')->fetchAll();
$recentUsers = $pdo->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="container wide" style="padding:0">
  <p style="margin-top:16px"><a href="../index.php">← Beranda SosialKita</a></p>
  <h1 style="margin-top:8px">🛡️ Dashboard Admin</h1>
  <p class="muted">Halo, <?= e($user['name']) ?> — pantau semua aktivitas di <?= e(APP_NAME) ?> dari sini.</p>

  <div class="admin-tabs">
    <a href="index.php" class="active">📊 Dashboard</a>
    <a href="posts.php">📝 Postingan</a>
    <a href="comments.php">💬 Komentar</a>
    <a href="users.php">👥 Pengguna</a>
  </div>

  <div class="stats">
    <?php foreach ($stats as $label => $n): ?>
      <div class="stat"><b><?= $n ?></b><span><?= e($label) ?></span></div>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <h2>Postingan terbaru</h2>
    <?php if (!$recentPosts): ?><p class="empty">Belum ada postingan.</p><?php endif; ?>
    <?php foreach ($recentPosts as $p): ?>
      <p><b><?= e($p['name']) ?></b> <span class="muted">@<?= e($p['username']) ?> · <?= e(timeAgo($p['created_at'])) ?></span><br>
      <?= e(excerpt($p['content'], 120)) ?>
      <span class="muted">— ❤️ <?= (int)$p['like_count'] ?> · 💬 <?= (int)$p['comment_count'] ?></span>
      <a href="../post.php?id=<?= (int)$p['id'] ?>">Lihat →</a></p>
    <?php endforeach; ?>
    <p><a href="posts.php">Kelola semua postingan →</a></p>
  </div>

  <div class="card">
    <h2>Pengguna terbaru</h2>
    <?php foreach ($recentUsers as $u): ?>
      <p><b><?= e($u['name']) ?></b> <span class="muted">@<?= e($u['username']) ?> · <?= e($u['role']) ?> · <?= e(timeAgo($u['created_at'])) ?></span></p>
    <?php endforeach; ?>
    <p><a href="users.php">Kelola semua pengguna →</a></p>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
