<?php
require_once __DIR__ . '/config.php';
requireLogin();
$user = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['post_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if ($post && ($post['user_id'] == $user['id'] || $user['role'] === 'admin')) {
        deleteImage($post['image']);
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        flash('Postingan berhasil dihapus.');
    } else {
        flash('Postingan tidak ditemukan atau kamu tidak berhak menghapusnya.');
    }
}
redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
