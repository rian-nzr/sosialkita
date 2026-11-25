<?php
require_once __DIR__ . '/config.php';
$user = currentUser();
$id = (int) ($_GET['id'] ?? 0);

// --- CREATE komentar ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    requireLogin();
    $content = trim($_POST['content'] ?? '');
    if ($content !== '' && strlen($content) <= 1000) {
        $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?,?,?)');
        $stmt->execute([$id, $user['id'], $content]);
        flash('Komentar ditambahkan.');
    } else {
        flash('Komentar tidak boleh kosong (maks 1000 karakter).', 'error');
    }
    redirect('post.php?id=' . $id);
}

// --- Ambil postingan ---
$stmt = $pdo->prepare('
    SELECT p.*, u.name, u.username,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count
    FROM posts p JOIN users u ON u.id = p.user_id WHERE p.id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { flash('Postingan tidak ditemukan.', 'error'); redirect('index.php'); }
$pageTitle = 'Postingan @' . $post['username'];

// --- Ambil komentar ---
$stmt = $pdo->prepare('
    SELECT c.*, u.name, u.username FROM comments c
    JOIN users u ON u.id = c.user_id WHERE c.post_id = ?
    ORDER BY c.created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
$liked = $user ? userLiked($id, (int)$user['id']) : false;
$editCommentId = isset($_GET['edit_comment']) ? (int) $_GET['edit_comment'] : 0;

include __DIR__ . '/includes/header.php';
?>
<article class="card">
  <div class="post-head">
    <a href="profile.php?u=<?= e($post['username']) ?>"><div class="avatar"><?= e(strtoupper(substr($post['name'], 0, 1))) ?></div></a>
    <div class="post-meta">
      <b><a href="profile.php?u=<?= e($post['username']) ?>"><?= e($post['username']) ?></a></b>
      <span><?= e(timeAgo($post['created_at'])) ?><?= $post['updated_at'] !== $post['created_at'] ? ' · diedit' : '' ?></span>
    </div>
    <a class="post-menu" href="index.php" title="Beranda">✕</a>
  </div>

  <?php if ($post['image']): ?>
    <div class="post-media"><img class="post-img" src="uploads/<?= e($post['image']) ?>" alt="Foto postingan"></div>
  <?php else: ?>
    <div class="post-text-tile"><p><?= e($post['content']) ?></p></div>
  <?php endif; ?>

  <div class="post-actions">
    <?php if ($user): ?>
      <form method="post" action="like.php" style="line-height:0"><input type="hidden" name="post_id" value="<?= $id ?>">
        <button class="icon-btn <?= $liked ? 'liked' : '' ?>" type="submit" title="Suka">
          <svg viewBox="0 0 24 24" fill="<?= $liked ? '#ed4956' : 'none' ?>" stroke="<?= $liked ? '#ed4956' : 'currentColor' ?>" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
        </button>
      </form>
    <?php endif; ?>
    <span title="Komentar"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5c-1.5 0-3-.4-4.2-1.1L3 20l1.1-5.3A8.5 8.5 0 1 1 21 11.5z"/></svg></span>
  </div>

  <div class="post-body">
    <?php if ((int)$post['like_count'] > 0): ?><div class="post-likes"><?= (int)$post['like_count'] ?> suka</div><?php endif; ?>
    <?php if ($post['image']): ?>
      <div class="post-caption"><b><?= e($post['username']) ?></b><p><?= e($post['content']) ?></p></div>
    <?php endif; ?>
    <div class="post-time"><?= e(date('d F Y', strtotime($post['created_at']))) ?></div>
  </div>

  <?php if ($user && ($user['id'] == $post['user_id'] || $user['role'] === 'admin')): ?>
  <div class="post-actions-row">
    <a class="btn btn-sm" href="edit_post.php?id=<?= $id ?>">✏️ Edit</a>
    <form class="inline-form" method="post" action="delete_post.php" onsubmit="return confirm('Yakin hapus postingan ini?')">
      <input type="hidden" name="post_id" value="<?= $id ?>">
      <button class="link-danger" type="submit">🗑️ Hapus</button>
    </form>
  </div>
  <?php endif; ?>
</article>

<div class="card pad">
  <h2 style="font-size:16px;margin-bottom:8px">💬 Komentar (<?= count($comments) ?>)</h2>
  <?php if (!$comments): ?><p class="empty">Belum ada komentar. Jadilah yang pertama!</p><?php endif; ?>
  <?php foreach ($comments as $c): ?>
    <div class="comment">
      <div class="avatar"><?= e(strtoupper(substr($c['name'], 0, 1))) ?></div>
      <div class="comment-body">
        <b><?= e($c['username']) ?></b> <?= e($c['content']) ?>
        <?php if ($editCommentId === (int)$c['id'] && $user && ($user['id'] == $c['user_id'] || $user['role'] === 'admin')): ?>
          <form method="post" action="edit_comment.php" style="margin-top:8px">
            <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
            <textarea name="content" required><?= e($c['content']) ?></textarea>
            <button class="btn btn-primary btn-sm" type="submit" style="margin-top:8px">Simpan</button>
            <a class="btn btn-sm" href="post.php?id=<?= $id ?>">Batal</a>
          </form>
        <?php else: ?>
          <div class="comment-meta"><?= e(timeAgo($c['created_at'])) ?><?= $c['updated_at'] !== $c['created_at'] ? ' · diedit' : '' ?>
            <?php if ($user && ($user['id'] == $c['user_id'] || $user['role'] === 'admin')): ?>
              · <a href="post.php?id=<?= $id ?>&edit_comment=<?= (int)$c['id'] ?>">Edit</a>
              · <form class="inline-form" method="post" action="delete_comment.php" onsubmit="return confirm('Yakin hapus komentar ini?')">
                  <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
                  <button class="link-danger" type="submit">Hapus</button>
                </form>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if ($user): ?>
    <form class="post-add-comment" method="post" style="border-top:1px solid #efefef;margin-top:8px">
      <input type="text" name="content" placeholder="Tambahkan komentar..." required maxlength="1000">
      <button type="submit" name="add_comment">Kirim</button>
    </form>
  <?php else: ?>
    <p style="margin-top:16px"><a href="login.php">Login</a> untuk ikut berkomentar.</p>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
