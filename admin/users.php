<?php
require_once __DIR__ . '/../config.php';
requireAdmin();
$pageTitle = 'Kelola Pengguna';
$me = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int) ($_POST['user_id'] ?? 0);
    if ($uid === (int) $me['id']) {
        flash('Kamu tidak bisa mengubah akunmu sendiri dari sini.');
    } elseif (isset($_POST['toggle_role'])) {
        $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        if ($row = $stmt->fetch()) {
            $new = $row['role'] === 'admin' ? 'user' : 'admin';
            $pdo->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$new, $uid]);
            flash('Role pengguna diubah menjadi ' . $new . '.');
        }
    } elseif (isset($_POST['delete_user'])) {
        // Hapus user + semua postingan/komentarnya (CASCADE di database).
        // Foto-foto postingannya ikut dibersihkan dari folder uploads.
        $stmt = $pdo->prepare('SELECT image FROM posts WHERE user_id = ?');
        $stmt->execute([$uid]);
        foreach ($stmt->fetchAll() as $r) deleteImage($r['image']);
        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
        flash('Pengguna berhasil dihapus beserta semua postingannya.');
    }
    redirect('users.php');
}

$users = $pdo->query('
    SELECT u.*, (SELECT COUNT(*) FROM posts WHERE user_id = u.id) AS post_count
    FROM users u ORDER BY u.created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="container wide" style="padding:0">
  <p style="margin-top:16px"><a href="../index.php">← Beranda SosialKita</a></p>
  <h1 style="margin-top:8px">👥 Kelola Pengguna</h1>
  <div class="admin-tabs">
    <a href="index.php">📊 Dashboard</a>
    <a href="posts.php">📝 Postingan</a>
    <a href="comments.php">💬 Komentar</a>
    <a href="users.php" class="active">👥 Pengguna</a>
  </div>
  <div class="card">
    <table class="table">
      <tr><th>Nama</th><th>Username / Email</th><th>Role</th><th>Post</th><th>Bergabung</th><th>Aksi</th></tr>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><b><?= e($u['name']) ?></b><?= $u['id'] == $me['id'] ? ' <span class="muted">(kamu)</span>' : '' ?></td>
        <td>@<?= e($u['username']) ?><br><span class="muted"><?= e($u['email']) ?></span></td>
        <td><?= e($u['role']) ?></td>
        <td><?= (int)$u['post_count'] ?></td>
        <td><?= e(date('d M Y', strtotime($u['created_at']))) ?></td>
        <td>
          <?php if ($u['id'] != $me['id']): ?>
          <form method="post" style="display:inline">
            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
            <button class="btn btn-sm" type="submit" name="toggle_role">Jadikan <?= $u['role'] === 'admin' ? 'user' : 'admin' ?></button>
          </form>
          <form method="post" style="display:inline" onsubmit="return confirm('Yakin hapus pengguna ini beserta SEMUA postingannya?')">
            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
            <button class="link-danger" type="submit" name="delete_user">Hapus</button>
          </form>
          <?php else: ?><span class="muted">—</span><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
