<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int) ($_POST['comment_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ?');
    $stmt->execute([$cid]);
    $c = $stmt->fetch();
    if ($c && ($c['user_id'] == $user['id'] || $user['role'] === 'admin')) {
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
        $stmt->execute([$cid]);
        flash('Komentar berhasil dihapus.');
        redirect('post.php?id=' . $c['post_id']);
    }
    flash('Komentar tidak ditemukan atau tidak bisa dihapus.', 'error');
}
redirect('index.php');
