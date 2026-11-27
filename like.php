<?php
require_once __DIR__ . '/config.php';
requireLogin();
$liked = false;
$count = 0;
$postId = 0;
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
            $liked = false;
        } else {
            $stmt = $pdo->prepare('INSERT INTO likes (post_id, user_id) VALUES (?,?)');
            $stmt->execute([$postId, $user['id']]);
            $liked = true;
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        $count = (int) $stmt->fetchColumn();
    }
}
// Kalau dipanggil lewat AJAX (like.js), balas JSON supaya halaman tidak reload.
if (($_POST['ajax'] ?? '') === '1') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'liked' => $liked, 'count' => $count, 'post_id' => $postId]);
    exit;
}
redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
