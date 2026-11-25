<?php
// Header bersama — dipanggil setelah config.php
// Catatan: header atas (topbar) sengaja DIHAPUS — navigasi utama sekarang
// hanya lewat bottom nav pill ala IG di footer.php (Home, Cari, Buat,
// Aktivitas, Profil). $base tetap dipakai footer untuk path admin/.
$base = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
$user = currentUser();
$flashMsg = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(APP_NAME) ?><?= isset($pageTitle) ? ' — ' . e($pageTitle) : '' ?></title>
  <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
  <script src="<?= $base ?>assets/js/image-preview.js" defer></script>
</head>
<body>
<main class="container<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? ' wide' : '' ?>">
  <?php if ($flashMsg): ?>
    <div class="alert"><?= e($flashMsg) ?></div>
  <?php endif; ?>
