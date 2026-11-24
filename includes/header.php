<?php
// Header bersama — dipanggil setelah config.php
// $base = '../' kalau file ini dipanggil dari folder admin/, '' kalau dari root.
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
</head>
<body>
<header class="topbar">
  <div class="topbar-inner">
    <a href="<?= $base ?>index.php" class="logo"><span class="logo-mark">◉</span> SosialKita</a>
    <nav class="nav">
      <?php if ($user): ?>
        <a href="<?= $base ?>index.php" title="Beranda"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg></a>
        <a href="<?= $base ?>profile.php?u=<?= e($user['username']) ?>" title="Profil"><span class="avatar" style="width:28px;height:28px;font-size:12px"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></span></a>
        <?php if ($user['role'] === 'admin'): ?>
          <a href="<?= $base ?>admin/index.php" class="nav-admin">🛡️ Admin</a>
        <?php endif; ?>
        <a href="<?= $base ?>logout.php" class="nav-logout">Keluar</a>
      <?php else: ?>
        <a href="<?= $base ?>login.php">Masuk</a>
        <a href="<?= $base ?>register.php" class="btn btn-primary btn-sm">Daftar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
  <?php if ($flashMsg): ?>
    <div class="alert"><?= e($flashMsg) ?></div>
  <?php endif; ?>
