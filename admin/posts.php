<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Kelola Postingan';
$user = currentUser();

// Hapus postingan (admin boleh hapus postingan siapa pun)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $id = (int) $_POST['post_id'];
    $stmt = $pdo->prepare('SELECT image FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        deleteImage($row['image']);
        $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        flash('Postingan berhasil dihapus oleh admin.');
    }
    redirect('posts.php');
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
        FROM posts p JOIN users u ON u.id = p.user_id
        WHERE p.content LIKE ? OR u.username LIKE ? OR u.name LIKE ?
        ORDER BY p.created_at DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
        FROM posts p JOIN users u ON u.id = p.user_id ORDER BY p.created_at DESC");
}
$posts = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="container wide" style="padding:0">
  <p style="margin-top:16px"><a href="../index.php">← Beranda Forum JMK48</a></p>
  <h1 style="margin-top:8px">📝 Kelola Postingan</h1>
  <div class="admin-tabs">
    <a href="index.php">📊 Dashboard</a>
    <a href="posts.php" class="active">📝 Postingan</a>
    <a href="comments.php">💬 Komentar</a>
    <a href="users.php">👥 Pengguna</a>
  </div>
  <form method="get" style="display:flex;gap:8px;margin:16px 0">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari isi postingan / username...">
    <button class="btn btn-primary" type="submit">Cari</button>
    <?php if ($search): ?><a class="btn" href="posts.php">Reset</a><?php endif; ?>
  </form>
  <div class="card">
    <?php if (!$posts): ?><p class="empty">Tidak ada postingan<?= $search ? ' yang cocok.' : '.' ?></p><?php endif; ?>
    <div class="table-wrap">
    <table class="table">
      <tr><th>Penulis</th><th>Isi</th><th class="col-hide-mobile">Foto</th><th>Like / Komen</th><th class="col-hide-mobile">Waktu</th><th>Aksi</th></tr>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><b><?= e($p['name']) ?></b><br><span class="muted">@<?= e($p['username']) ?></span></td>
        <td><?= e(excerpt($p['content'], 140)) ?></td>
        <td class="col-hide-mobile"><?= $p['image'] ? '🖼️ Ya' : '—' ?></td>
        <td>❤️ <?= (int)$p['like_count'] ?><br>💬 <?= (int)$p['comment_count'] ?></td>
        <td class="col-hide-mobile"><?= e(timeAgo($p['created_at'])) ?></td>
        <td>
          <a href="../post.php?id=<?= (int)$p['id'] ?>">Lihat</a><br>
          <form method="post" onsubmit="return confirm('Yakin hapus postingan ini?')">
            <input type="hidden" name="post_id" value="<?= (int)$p['id'] ?>">
            <button class="link-danger" type="submit" name="delete_post">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
