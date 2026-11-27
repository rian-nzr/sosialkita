<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
$pageTitle = 'Buat postingan';

// --- CREATE post ---
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $content = trim($_POST['content'] ?? '');
    if ($content === '') $error = 'Tulis sesuatu dulu sebelum posting.';
    elseif (strlen($content) > 2000) $error = 'Postingan maksimal 2000 karakter.';
    if (!$error) {
        $image = uploadImage($_FILES['image'] ?? [], $error);
        if (!$error) {
            $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, image) VALUES (?,?,?)');
            $stmt->execute([$user['id'], $content, $image]);
            flash('Postinganmu sudah tayang! 🎉');
            redirect('index.php');
        }
    }
}
include __DIR__ . '/includes/header.php';
?>

<div class="card pad">
  <div style="display:flex;gap:10px;align-items:center;margin-bottom:14px">
    <?= avatarHtml($user) ?>
    <div>
      <b><?= e($user['name']) ?></b>
      <div class="muted" style="font-size:13px">@<?= e($user['username']) ?></div>
    </div>
    <span style="flex:1"></span>
    <a class="btn btn-sm" href="index.php">✕ Batal</a>
  </div>

  <h2 style="margin-bottom:12px">Buat postingan</h2>

  <form method="post" enctype="multipart/form-data">
    <textarea name="content" rows="6" placeholder="Ceritakan sesuatu..." required maxlength="2000" autofocus style="width:100%;resize:vertical"><?= e($_POST['content'] ?? '') ?></textarea>
    <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <div style="display:flex;gap:8px;align-items:center;margin-top:12px;flex-wrap:wrap">
      <label style="margin:0;font-weight:400;color:var(--muted);cursor:pointer"><span class="file-label-text">📷 Tambah foto</span> <input type="file" name="image" accept="image/*" style="display:none" data-preview="create-preview-wrap"></label>
      <span style="flex:1"></span>
      <button class="btn btn-primary" type="submit" name="create_post">Bagikan</button>
    </div>
    <div class="img-preview-wrap" id="create-preview-wrap" hidden>
      <img alt="Pratinjau foto">
      <button type="button" class="img-preview-remove" data-preview-remove title="Batalkan foto">✕</button>
      <p class="img-preview-note">Pratinjau — foto baru diunggah setelah kamu tekan Bagikan.</p>
    </div>
    <p class="muted" style="margin-top:10px">Tulis sampai 2000 karakter. Foto (kalau ada) maks 2 MB, JPG/PNG/GIF/WebP.</p>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
