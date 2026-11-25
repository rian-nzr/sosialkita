<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int) ($_POST['comment_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ?');
    $stmt->execute([$cid]);
    $c = $stmt->fetch();
    if ($c && $content !== '' && ($c['user_id'] == $user['id'] || $user['role'] === 'admin')) {
        $stmt = $pdo->prepare('UPDATE comments SET content = ? WHERE id = ?');
        $stmt->execute([$content, $cid]);
        flash('Komentar berhasil diedit.');
        redirect('post.php?id=' . $c['post_id']);
    }
    flash('Komentar tidak ditemukan atau tidak bisa diedit.', 'error');
    redirect('index.php');
}
redirect('index.php');
