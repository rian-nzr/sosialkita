<?php
require_once __DIR__ . '/config.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = (int) ($_POST['post_id'] ?? 0);
    $user = currentUser();
    $stmt = $pdo->prepare('SELECT id FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    if ($stmt->fetch()) {
        // Toggle like: kalau sudah like -> unlike, kalau belum -> like
        $stmt = $pdo->prepare('SELECT id FROM likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $user['id']]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
            $stmt->execute([$postId, $user['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO likes (post_id, user_id) VALUES (?,?)');
            $stmt->execute([$postId, $user['id']]);
        }
    }
}
redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
