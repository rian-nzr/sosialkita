<?php
// Halaman Cari (ikon kaca pembesar di bottom nav) — cari pengguna & postingan.
require_once __DIR__ . '/config.php';
$pageTitle = 'Cari';
$q = trim($_GET['q'] ?? '');
$foundUsers = [];
$foundPosts = [];
// Kalau belum nyari apa-apa, tampilkan konten rekomendasi ala IG Explore.
$explorePosts = [];
$suggestedUsers = [];
if ($q === '') {
    // Postingan rekomendasi: paling banyak disukai dulu, lalu terbaru.
    $stmt = $pdo->query('
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
        FROM posts p JOIN users u ON u.id = p.user_id
        ORDER BY like_count DESC, p.created_at DESC LIMIT 12');
    $explorePosts = $stmt->fetchAll();
    // Pengguna yang disarankan: paling aktif posting, bukan diri sendiri.
    $me = currentUser();
    $stmt = $pdo->prepare('
        SELECT u.id, u.name, u.username, u.bio, COUNT(p.id) AS post_count
        FROM users u LEFT JOIN posts p ON p.user_id = u.id
        WHERE u.id != ? GROUP BY u.id ORDER BY post_count DESC, u.name LIMIT 5');
    $stmt->execute([$me ? $me['id'] : 0]);
    $suggestedUsers = $stmt->fetchAll();
}
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT id, name, username, bio FROM users WHERE name LIKE ? OR username LIKE ? ORDER BY name LIMIT 10');
    $stmt->execute(["%$q%", "%$q%"]);
    $foundUsers = $stmt->fetchAll();
    $stmt = $pdo->prepare('
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
        FROM posts p JOIN users u ON u.id = p.user_id
        WHERE p.content LIKE ? ORDER BY p.created_at DESC LIMIT 20');
    $stmt->execute(["%$q%"]);
    $foundPosts = $stmt->fetchAll();
}
include __DIR__ . '/includes/header.php';
?>
<div class="card pad" style="text-align:center">
  <div class="logo" style="justify-content:center;margin-bottom:12px"><span class="logo-mark">◉</span> SosialKita</div>
  <form method="get" style="display:flex;gap:8px">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari pengguna atau postingan..." autofocus>
    <button class="btn btn-primary" type="submit">Cari</button>
  </form>
</div>

<?php if ($q !== ''): ?>
  <h2 style="font-size:16px;margin:16px 4px 4px">Pengguna (<?= count($foundUsers) ?>)</h2>
  <?php if (!$foundUsers): ?><p class="empty">Tidak ada pengguna yang cocok.</p><?php endif; ?>
  <?php foreach ($foundUsers as $u): ?>
    <a href="profile.php?u=<?= e($u['username']) ?>">
      <div class="card pad" style="display:flex;gap:12px;align-items:center">
        <div class="avatar"><?= e(strtoupper(substr($u['name'], 0, 1))) ?></div>
        <div><b><?= e($u['username']) ?></b><br><span class="muted"><?= e($u['name']) ?> · <?= e($u['bio'] ?: 'Belum ada bio.') ?></span></div>
      </div>
    </a>
  <?php endforeach; ?>

  <h2 style="font-size:16px;margin:16px 4px 4px">Postingan (<?= count($foundPosts) ?>)</h2>
  <?php if (!$foundPosts): ?><p class="empty">Tidak ada postingan yang cocok.</p><?php endif; ?>
  <?php foreach ($foundPosts as $p): ?>
    <article class="card">
      <div class="post-head">
        <a href="profile.php?u=<?= e($p['username']) ?>"><div class="avatar"><?= e(strtoupper(substr($p['name'], 0, 1))) ?></div></a>
        <div class="post-meta">
          <b><a href="profile.php?u=<?= e($p['username']) ?>"><?= e($p['username']) ?></a></b>
          <span><?= e(timeAgo($p['created_at'])) ?></span>
        </div>
      </div>
      <?php if ($p['image']): ?>
        <div class="post-media"><img class="post-img" src="uploads/<?= e($p['image']) ?>" alt="Foto postingan"></div>
      <?php else: ?>
        <div class="post-text-tile"><p><?= e($p['content']) ?></p></div>
      <?php endif; ?>
      <div class="post-body">
        <?php if ((int)$p['like_count'] > 0): ?><div class="post-likes"><?= (int)$p['like_count'] ?> suka</div><?php endif; ?>
        <?php if ($p['image']): ?><div class="post-caption"><b><?= e($p['username']) ?></b><p><?= e($p['content']) ?></p></div><?php endif; ?>
        <a class="post-comments-link" href="post.php?id=<?= (int)$p['id'] ?>">💬 <?= (int)$p['comment_count'] ?> komentar — buka postingan →</a>
      </div>
    </article>
  <?php endforeach; ?>
<?php else: ?>
  <?php if ($suggestedUsers): ?>
    <h2 style="font-size:16px;margin:20px 4px 4px">Disarankan untuk Anda</h2>
    <?php foreach ($suggestedUsers as $u): ?>
      <a href="profile.php?u=<?= e($u['username']) ?>">
        <div class="card pad" style="display:flex;gap:12px;align-items:center">
          <div class="avatar"><?= e(strtoupper(substr($u['name'], 0, 1))) ?></div>
          <div style="flex:1;min-width:0">
            <b><?= e($u['username']) ?></b><br>
            <span class="muted"><?= e($u['name']) ?> · <?= (int)$u['post_count'] ?> postingan<?= $u['bio'] ? ' · ' . e($u['bio']) : '' ?></span>
          </div>
          <span class="btn btn-primary btn-sm">Lihat</span>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>

  <h2 style="font-size:16px;margin:20px 4px 4px">✨ Jelajahi — Rekomendasi untukmu</h2>
  <?php if (!$explorePosts): ?><p class="empty">Belum ada postingan untuk dijelajahi. Jadilah yang pertama posting! ✨</p><?php endif; ?>
  <div class="profile-grid">
    <?php foreach ($explorePosts as $p): ?>
      <a href="post.php?id=<?= (int)$p['id'] ?>" title="<?= e($p['username']) ?> — <?= e(substr($p['content'], 0, 60)) ?>">
        <?php if ($p['image']): ?>
          <img src="uploads/<?= e($p['image']) ?>" alt="Foto postingan">
        <?php else: ?>
          <div class="grid-text"><?= e(substr($p['content'], 0, 80)) ?></div>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
  <p class="muted" style="text-align:center;margin:16px 0 4px">Ketuk foto untuk buka postingan · diurutkan dari yang paling disukai</p>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
