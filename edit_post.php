<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { flash('Postingan tidak ditemukan.', 'error'); redirect('index.php'); }
if ($post['user_id'] != $user['id'] && $user['role'] !== 'admin') {
    flash('Kamu hanya bisa mengedit postinganmu sendiri.', 'error');
    redirect('index.php');
}
$pageTitle = 'Edit Postingan';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    if ($content === '') $error = 'Isi postingan tidak boleh kosong.';
    else {
        $image = $post['image'];
        if (!empty($_FILES['image']['name'])) {
            $new = uploadImage($_FILES['image'], $error);
            if (!$error && $new) { deleteImage($image); $image = $new; }
        }
        if (isset($_POST['remove_image'])) { deleteImage($image); $image = null; }
        if (!$error) {
            $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = ? WHERE id = ?');
            $stmt->execute([$content, $image, $id]);
            flash('Postingan berhasil diedit.');
            redirect('post.php?id=' . $id);
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h1>Edit postingan</h1>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Isi postingan</label>
    <textarea name="content" required><?= e($_POST['content'] ?? $post['content']) ?></textarea>
    <?php if ($post['image']): ?>
      <p style="margin-top:12px">Foto saat ini:</p>
      <img class="post-img" src="uploads/<?= e($post['image']) ?>" alt="Foto lama">
      <label><input type="checkbox" name="remove_image" value="1"> Hapus foto ini</label>
    <?php endif; ?>
    <label>Ganti foto (opsional)</label>
    <input type="file" name="image" accept="image/*" data-preview="edit-preview-wrap">
    <div class="img-preview-wrap" id="edit-preview-wrap" hidden>
      <img alt="Pratinjau foto baru">
      <button type="button" class="img-preview-remove" data-preview-remove title="Batalkan foto baru">✕</button>
      <p class="img-preview-note">Pratinjau foto baru — baru tersimpan setelah kamu tekan Simpan perubahan.</p>
    </div>
    <button class="btn btn-primary" type="submit" style="margin-top:14px">Simpan perubahan</button>
    <a class="btn" href="post.php?id=<?= $id ?>">Batal</a>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
