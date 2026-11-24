<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Beranda';
$user = currentUser();

// --- CREATE post (dari form di beranda) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    requireLogin();
    $content = trim($_POST['content'] ?? '');
    $error = '';
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

// --- Ambil semua postingan + info penulis + jumlah like/komentar ---
// Kalau ada ?q= dari kotak Cari di navbar, filter berdasarkan isi/penulis.
$searchQ = trim($_GET['q'] ?? '');
if ($searchQ !== '') {
    $stmt = $pdo->prepare("
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
        FROM posts p JOIN users u ON u.id = p.user_id
        WHERE p.content LIKE ? OR u.username LIKE ? OR u.name LIKE ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute(["%$searchQ%", "%$searchQ%", "%$searchQ%"]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, u.name, u.username,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
        FROM posts p JOIN users u ON u.id = p.user_id
        ORDER BY p.created_at DESC
    ");
}
$posts = $stmt->fetchAll();

// --- Penulis unik untuk baris Stories ala IG ---
$storyUsers = [];
foreach ($posts as $p) $storyUsers[$p['username']] = $p;
include __DIR__ . '/includes/header.php';
?>

<?php if ($user): ?>
  <?php $hour = (int)date('G'); $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam')); ?>
  <div class="greeting">
    <div class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div>
    <div>
      <p><?= e($greet) ?> 👋</p>
      <h1><?= e($user['name']) ?></h1>
    </div>
    <span style="flex:1"></span>
    <a href="activity.php" title="Aktivitas" style="font-size:22px">🔔</a>
  </div>
  <div class="stories">
    <a class="story" href="profile.php?u=<?= e($user['username']) ?>">
      <div class="ring"><div class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div></div>
      <span>Cerita Anda</span>
    </a>
    <?php foreach ($storyUsers as $su): if ($su['username'] === $user['username']) continue; ?>
    <a class="story" href="profile.php?u=<?= e($su['username']) ?>">
      <div class="ring"><div class="avatar"><?= e(strtoupper(substr($su['name'], 0, 1))) ?></div></div>
      <span><?= e($su['username']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="card pad" id="composer">
    <form method="post" enctype="multipart/form-data">
      <div style="display:flex;gap:10px;align-items:center">
        <div class="avatar"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></div>
        <input type="text" name="content" placeholder="Buat postingan baru, <?= e($user['name']) ?>..." required style="border:none;background:transparent;flex:1" maxlength="2000">
      </div>
      <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
      <div style="display:flex;gap:8px;align-items:center;margin-top:10px">
        <label style="margin:0;font-weight:400;color:var(--muted);cursor:pointer">📷 <input type="file" name="image" accept="image/*" style="display:none" onchange="this.parentElement.firstChild.textContent='📷 Foto dipilih'"></label>
        <span style="flex:1"></span>
        <button class="btn btn-primary btn-sm" type="submit" name="create_post">Bagikan</button>
      </div>
      <p class="muted" style="margin-top:8px">Tulis di kolom atas, tambah foto kalau mau (maks 2 MB). Untuk caption panjang, pakai halaman detail postingan.</p>
    </form>
  </div>
<?php else: ?>
  <div class="card pad" style="text-align:center">
    <h2>Selamat datang di <?= e(APP_NAME) ?> 👋</h2>
    <p class="muted">Sosial media sederhana: posting teks &amp; foto, like, dan komentar.</p>
    <a class="btn btn-primary" href="register.php">Daftar gratis</a>
    <a class="btn" href="login.php">Masuk</a>
  </div>
<?php endif; ?>

<?php if ($searchQ !== ''): ?>
  <p style="padding:12px 4px">Hasil pencarian untuk <b>"<?= e($searchQ) ?>"</b> — <?= count($posts) ?> postingan · <a href="index.php">Reset</a></p>
<?php endif; ?>
<?php if (!$posts): ?>
  <p class="empty">Belum ada postingan<?= $searchQ !== '' ? ' yang cocok.' : '. Jadilah yang pertama posting! ✨' ?></p>
<?php endif; ?>

<?php foreach ($posts as $p): $liked = $user ? userLiked((int)$p['id'], (int)$user['id']) : false; ?>
  <article class="card">
    <div class="post-head">
      <a href="profile.php?u=<?= e($p['username']) ?>"><div class="avatar"><?= e(strtoupper(substr($p['name'], 0, 1))) ?></div></a>
      <div class="post-meta">
        <b><a href="profile.php?u=<?= e($p['username']) ?>"><?= e($p['username']) ?></a></b>
        <span><?= e(timeAgo($p['created_at'])) ?><?= $p['updated_at'] !== $p['created_at'] ? ' · diedit' : '' ?></span>
      </div>
      <?php if ($user && ($user['id'] == $p['user_id'] || $user['role'] === 'admin')): ?>
        <a class="post-menu" href="edit_post.php?id=<?= (int)$p['id'] ?>" title="Edit">•••</a>
      <?php endif; ?>
    </div>

    <?php if ($p['image']): ?>
      <div class="post-media"><img class="post-img" src="uploads/<?= e($p['image']) ?>" alt="Foto postingan"></div>
    <?php else: ?>
      <div class="post-text-tile"><p><?= e($p['content']) ?></p></div>
    <?php endif; ?>

    <div class="post-actions">
      <?php if ($user): ?>
        <form method="post" action="like.php" style="line-height:0"><input type="hidden" name="post_id" value="<?= (int)$p['id'] ?>">
          <button class="icon-btn <?= $liked ? 'liked' : '' ?>" type="submit" title="Suka">
            <svg viewBox="0 0 24 24" fill="<?= $liked ? '#ed4956' : 'none' ?>" stroke="<?= $liked ? '#ed4956' : 'currentColor' ?>" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
          </button>
        </form>
      <?php else: ?>
        <span title="Suka"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg></span>
      <?php endif; ?>
      <a href="post.php?id=<?= (int)$p['id'] ?>" title="Komentar"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5c-1.5 0-3-.4-4.2-1.1L3 20l1.1-5.3A8.5 8.5 0 1 1 21 11.5z"/></svg></a>
      <a href="post.php?id=<?= (int)$p['id'] ?>" title="Bagikan"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></svg></a>
    </div>

    <div class="post-body">
      <?php if ((int)$p['like_count'] > 0): ?><div class="post-likes"><?= (int)$p['like_count'] ?> suka</div><?php endif; ?>
      <?php if ($p['image']): ?>
        <div class="post-caption"><b><?= e($p['username']) ?></b><p><?= e($p['content']) ?></p></div>
      <?php endif; ?>
      <?php if ((int)$p['comment_count'] > 0): ?>
        <a class="post-comments-link" href="post.php?id=<?= (int)$p['id'] ?>">Lihat semua <?= (int)$p['comment_count'] ?> komentar</a>
      <?php endif; ?>
      <div class="post-time"><?= e(date('d F Y', strtotime($p['created_at']))) ?></div>
    </div>

    <?php if ($user): ?>
    <form class="post-add-comment" method="post" action="post.php?id=<?= (int)$p['id'] ?>">
      <input type="text" name="content" placeholder="Tambahkan komentar..." required maxlength="1000">
      <button type="submit" name="add_comment">Kirim</button>
    </form>
    <?php endif; ?>

    <?php if ($user && ($user['id'] == $p['user_id'] || $user['role'] === 'admin')): ?>
    <div class="post-actions-row">
      <a class="btn btn-sm" href="edit_post.php?id=<?= (int)$p['id'] ?>">✏️ Edit</a>
      <form class="inline-form" method="post" action="delete_post.php" onsubmit="return confirm('Yakin hapus postingan ini? Komentar & likenya ikut terhapus.')">
        <input type="hidden" name="post_id" value="<?= (int)$p['id'] ?>">
        <button class="link-danger" type="submit">🗑️ Hapus</button>
      </form>
    </div>
    <?php endif; ?>
  </article>
<?php endforeach; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
