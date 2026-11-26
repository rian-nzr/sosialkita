<?php
require_once __DIR__ . '/config.php';
$username = $_GET['u'] ?? '';
$stmt = $pdo->prepare('SELECT id, name, username, email, bio, avatar, role, created_at FROM users WHERE username = ?');
$stmt->execute([$username]);
$profile = $stmt->fetch();
if (!$profile) { flash('Pengguna tidak ditemukan.', 'error'); redirect('index.php'); }
$pageTitle = '@' . $profile['username'];
$user = currentUser();

// Edit profil: nama + bio + foto profil (pemilik profil / admin).
// Username TIDAK bisa diganti — dipakai di URL profil (?u=...) dan harus unik.
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile']) && $user && ($user['id'] == $profile['id'] || $user['role'] === 'admin')) {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    if ($name === '') $error = 'Nama tidak boleh kosong.';
    elseif ((function_exists('mb_strlen') ? mb_strlen($name) : strlen($name)) > 100) $error = 'Nama maksimal 100 karakter.';
    $avatar = $profile['avatar'] ?? null;
    if (!$error && !empty($_POST['remove_avatar'])) {
        deleteImage($avatar);
        $avatar = null;
    }
    if (!$error) {
        $newAvatar = uploadImage($_FILES['avatar'] ?? [], $error);
        if (!$error && $newAvatar) {
            deleteImage($avatar);
            $avatar = $newAvatar;
        }
    }
    if (!$error) {
        $stmt = $pdo->prepare('UPDATE users SET name = ?, bio = ?, avatar = ? WHERE id = ?');
        $stmt->execute([$name, substr($bio, 0, 255), $avatar, $profile['id']]);
        flash('Profil berhasil disimpan.');
        redirect('profile.php?u=' . $profile['username']);
    }
    // Gagal validasi: tampilkan lagi isian yang sudah diketik.
    $profile['name'] = $name;
    $profile['bio'] = $bio;
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
    <?= avatarHtml($profile, 'lg') ?>
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
        <button type="button" class="btn btn-sm" id="edit-profile-toggle" style="margin-top:10px"><?= !empty($error) ? '✕ Batal' : '✏️ Edit Profil' ?></button>
        <form method="post" enctype="multipart/form-data" id="edit-profile-form" style="max-width:320px;margin-top:14px"<?= empty($error) ? ' hidden' : '' ?>>
          <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
          <label style="font-size:13px;font-weight:600">Nama</label>
          <input type="text" name="name" maxlength="100" required value="<?= e($profile['name']) ?>" style="margin:4px 0 10px">
          <label style="font-size:13px;font-weight:600">Bio</label>
          <input type="text" name="bio" maxlength="255" value="<?= e($profile['bio'] ?? '') ?>" placeholder="Ceritakan sedikit tentangmu..." style="margin:4px 0 10px">
          <label style="font-size:13px;font-weight:600">Foto profil</label>
          <div style="display:flex;gap:10px;align-items:center;margin:6px 0 10px">
            <?= avatarHtml($profile) ?>
            <label style="margin:0;font-weight:400;color:var(--muted);cursor:pointer"><span class="file-label-text">📷 <?= !empty($profile['avatar']) ? 'Ganti foto' : 'Pilih foto' ?></span> <input type="file" name="avatar" accept="image/*" style="display:none" data-preview="avatar-preview-wrap"></label>
          </div>
          <div class="img-preview-wrap" id="avatar-preview-wrap" hidden>
            <img alt="Pratinjau foto profil">
            <button type="button" class="img-preview-remove" data-preview-remove title="Batalkan foto">✕</button>
            <p class="img-preview-note">Pratinjau — foto baru disimpan setelah kamu tekan Simpan profil.</p>
          </div>
          <?php if (!empty($profile['avatar'])): ?>
            <label style="font-weight:400;color:var(--muted);font-size:13px;display:block;margin-bottom:10px"><input type="checkbox" name="remove_avatar" value="1"> Hapus foto profil (kembali ke huruf awal)</label>
          <?php endif; ?>
          <button class="btn btn-primary btn-sm" type="submit" name="save_profile">Simpan profil</button>
          <p class="muted" style="font-size:12px;margin-top:8px">Username @<?= e($profile['username']) ?> tidak bisa diganti. Foto maksimal 2 MB (JPG/PNG/GIF/WebP).</p>
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
          <div class="grid-text"><?= e(excerpt($p['content'], 80)) ?></div>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php if ($user && ($user['id'] == $profile['id'] || $user['role'] === 'admin')): ?>
<script>
(function () {
  var btn = document.getElementById('edit-profile-toggle');
  var form = document.getElementById('edit-profile-form');
  if (!btn || !form) return;
  btn.addEventListener('click', function () {
    var open = form.hidden;
    form.hidden = !open;
    btn.textContent = open ? '✕ Batal' : '✏️ Edit Profil';
    if (open) form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
})();
</script>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
