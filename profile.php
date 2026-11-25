<?php
require_once __DIR__ . '/config.php';
$username = $_GET['u'] ?? '';
$stmt = $pdo->prepare('SELECT id, name, username, email, bio, role, created_at FROM users WHERE username = ?');
$stmt->execute([$username]);
$profile = $stmt->fetch();
if (!$profile) { flash('Pengguna tidak ditemukan.', 'error'); redirect('index.php'); }
$pageTitle = '@' . $profile['username'];
$user = currentUser();

// Edit bio (pemilik profil / admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_bio']) && $user && ($user['id'] == $profile['id'] || $user['role'] === 'admin')) {
    $bio = trim($_POST['bio'] ?? '');
    $stmt = $pdo->prepare('UPDATE users SET bio = ? WHERE id = ?');
    $stmt->execute([substr($bio, 0, 255), $profile['id']]);
    flash('Bio berhasil disimpan.');
    redirect('profile.php?u=' . $profile['username']);
}

$stmt = $pdo->prepare('
    SELECT p.*, (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
    FROM posts p WHERE p.user_id = ? ORDER BY p.created_at DESC');
$stmt->execute([$profile['id']]);
$posts = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="container wide" style="padding:0">
  <div class="profile-head">
    <div class="avatar lg"><?= e(strtoupper(substr($profile['name'], 0, 1))) ?></div>
    <div class="profile-info">
      <h1><?= e($profile['username']) ?></h1>
      <div class="profile-stats">
        <span><b><?= count($posts) ?></b> postingan</span>
        <span><b><?= e($profile['role']) ?></b></span>
      </div>
      <p><b><?= e($profile['name']) ?></b></p>
      <p><?= e($profile['bio'] ?: 'Belum ada bio.') ?></p>
      <p class="muted">Bergabung <?= e(date('d M Y', strtotime($profile['created_at']))) ?></p>
      <?php if ($user && $user['role'] === 'admin'): ?>
        <a class="btn btn-sm" href="admin/index.php" style="margin-top:10px">🛡️ Dashboard Admin</a>
      <?php endif; ?>
      <?php if ($user && $user['id'] == $profile['id']): ?>
        <a class="btn btn-sm" href="logout.php" style="margin-top:10px">Keluar</a>
      <?php endif; ?>
      <?php if ($user && ($user['id'] == $profile['id'] || $user['role'] === 'admin')): ?>
        <form method="post" style="max-width:320px;margin-top:10px">
          <input type="text" name="bio" maxlength="255" value="<?= e($profile['bio'] ?? '') ?>" placeholder="Edit bio...">
          <button class="btn btn-primary btn-sm" type="submit" name="save_bio" style="margin-top:8px">Simpan bio</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <hr style="border:none;border-top:1px solid var(--border)">
  <p style="text-align:center;padding:12px 0 0;font-weight:600;letter-spacing:1px;font-size:12px;color:var(--muted)">▦ POSTINGAN</p>

  <?php if (!$posts): ?><p class="empty">Belum ada postingan.</p><?php endif; ?>
  <div class="profile-grid">
    <?php foreach ($posts as $p): ?>
      <a href="post.php?id=<?= (int)$p['id'] ?>" title="<?= e(substr($p['content'], 0, 60)) ?>">
        <?php if ($p['image']): ?>
          <img src="uploads/<?= e($p['image']) ?>" alt="Foto postingan">
        <?php else: ?>
          <div class="grid-text"><?= e(substr($p['content'], 0, 80)) ?></div>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
