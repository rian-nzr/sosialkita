<?php
// Halaman Aktivitas (ikon hati di navbar bawah) — like & komentar terbaru
// di postingan milik user yang sedang login.
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$pageTitle = 'Aktivitas';

// Like terbaru di postingan milik user
$stmt = $pdo->prepare('
    SELECT l.created_at, u.name, u.username, p.id AS post_id,
           LEFT(p.content, 60) AS post_content
    FROM likes l
    JOIN posts p ON p.id = l.post_id
    JOIN users u ON u.id = l.user_id
    WHERE p.user_id = ? AND l.user_id != ?
    ORDER BY l.created_at DESC LIMIT 20');
$stmt->execute([$user['id'], $user['id']]);
$likes = $stmt->fetchAll();

// Komentar terbaru di postingan milik user
$stmt = $pdo->prepare('
    SELECT c.created_at, c.content, u.name, u.username, p.id AS post_id,
           LEFT(p.content, 60) AS post_content
    FROM comments c
    JOIN posts p ON p.id = c.post_id
    JOIN users u ON u.id = c.user_id
    WHERE p.user_id = ? AND c.user_id != ?
    ORDER BY c.created_at DESC LIMIT 20');
$stmt->execute([$user['id'], $user['id']]);
$comments = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<div class="card pad">
  <h1 style="font-size:18px">❤️ Aktivitas</h1>
  <p class="muted">Like &amp; komentar terbaru di postinganmu.</p>
</div>

<?php if (!$likes && !$comments): ?>
  <p class="empty">Belum ada aktivitas. Kalau ada yang like atau komentar di postinganmu, muncul di sini. ✨</p>
<?php endif; ?>

<?php foreach ($likes as $l): ?>
  <div class="card pad" style="display:flex;gap:12px;align-items:center">
    <div class="avatar"><?= e(strtoupper(substr($l['name'], 0, 1))) ?></div>
    <div style="flex:1">
      <b><?= e($l['username']) ?></b> menyukai postinganmu
      <span class="muted">— "<?= e($l['post_content']) ?>…"</span><br>
      <span class="muted"><?= e(timeAgo($l['created_at'])) ?></span>
    </div>
    <a class="btn btn-sm" href="post.php?id=<?= (int)$l['post_id'] ?>">Lihat</a>
  </div>
<?php endforeach; ?>

<?php foreach ($comments as $c): ?>
  <div class="card pad" style="display:flex;gap:12px;align-items:center">
    <div class="avatar"><?= e(strtoupper(substr($c['name'], 0, 1))) ?></div>
    <div style="flex:1">
      <b><?= e($c['username']) ?></b> mengomentari postinganmu: "<?= e($c['content']) ?>"
      <span class="muted">— di "<?= e($c['post_content']) ?>…"</span><br>
      <span class="muted"><?= e(timeAgo($c['created_at'])) ?></span>
    </div>
    <a class="btn btn-sm" href="post.php?id=<?= (int)$c['post_id'] ?>">Balas</a>
  </div>
<?php endforeach; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
