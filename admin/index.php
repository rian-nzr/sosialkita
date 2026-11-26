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
    SELECT p.*, u.name, u.username, u.avatar,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
    FROM posts p JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC LIMIT 5')->fetchAll();
$recentUsers = $pdo->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="container wide" style="padding:0">
  <p style="margin-top:16px"><a href="../index.php">← Beranda Forum JMK48</a></p>
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
    <div class="card-head"><h2>Postingan terbaru</h2></div>
    <?php if (!$recentPosts): ?><p class="empty">Belum ada postingan.</p><?php endif; ?>
    <?php foreach ($recentPosts as $p): ?>
      <div class="list-row">
        <?= avatarHtml($p) ?>
        <div class="list-body">
          <b><?= e($p['name']) ?></b> <span class="list-meta">@<?= e($p['username']) ?> · <?= e(timeAgo($p['created_at'])) ?></span>
          <p><?= e(excerpt($p['content'], 120)) ?></p>
          <span class="list-meta">❤️ <?= (int)$p['like_count'] ?> · 💬 <?= (int)$p['comment_count'] ?></span>
        </div>
        <a class="list-action" href="../post.php?id=<?= (int)$p['id'] ?>">Lihat</a>
      </div>
    <?php endforeach; ?>
    <div class="card-foot"><a href="posts.php">Kelola semua postingan →</a></div>
  </div>

  <div class="card">
    <div class="card-head"><h2>Pengguna terbaru</h2></div>
    <?php foreach ($recentUsers as $u): ?>
      <div class="list-row">
        <?= avatarHtml($u) ?>
        <div class="list-body">
          <b><?= e($u['name']) ?></b> <span class="list-meta">@<?= e($u['username']) ?> · <?= e($u['role']) ?> · <?= e(timeAgo($u['created_at'])) ?></span>
        </div>
        <a class="list-action" href="../profile.php?u=<?= e($u['username']) ?>">Lihat</a>
      </div>
    <?php endforeach; ?>
    <div class="card-foot"><a href="users.php">Kelola semua pengguna →</a></div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
