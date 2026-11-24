<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Kelola Komentar';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([(int) $_POST['comment_id']]);
    flash('Komentar berhasil dihapus oleh admin.');
    redirect('comments.php');
}

$comments = $pdo->query('
    SELECT c.*, u.name, u.username, p.content AS post_content
    FROM comments c
    JOIN users u ON u.id = c.user_id
    JOIN posts p ON p.id = c.post_id
    ORDER BY c.created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="container wide" style="padding:0">
  <p style="margin-top:16px"><a href="../index.php">← Beranda SosialKita</a></p>
  <h1 style="margin-top:8px">💬 Kelola Komentar</h1>
  <div class="admin-tabs">
    <a href="index.php">📊 Dashboard</a>
    <a href="posts.php">📝 Postingan</a>
    <a href="comments.php" class="active">💬 Komentar</a>
    <a href="users.php">👥 Pengguna</a>
  </div>
  <div class="card">
    <?php if (!$comments): ?><p class="empty">Belum ada komentar.</p><?php endif; ?>
    <table class="table">
      <tr><th>Penulis</th><th>Komentar</th><th>Di postingan</th><th>Waktu</th><th>Aksi</th></tr>
      <?php foreach ($comments as $c): ?>
      <tr>
        <td><b><?= e($c['name']) ?></b><br><span class="muted">@<?= e($c['username']) ?></span></td>
        <td><?= e($c['content']) ?></td>
        <td><?= e(excerpt($c['post_content'], 80)) ?><br><a href="../post.php?id=<?= (int)$c['post_id'] ?>">Lihat post →</a></td>
        <td><?= e(timeAgo($c['created_at'])) ?></td>
        <td>
          <form method="post" onsubmit="return confirm('Yakin hapus komentar ini?')">
            <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
            <button class="link-danger" type="submit" name="delete_comment">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
