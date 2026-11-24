<?php
// Header bersama — dipanggil setelah config.php
// $base = '../' kalau file ini dipanggil dari folder admin/, '' kalau dari root.
$base = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
$user = currentUser();
$flashMsg = flash();
$searchQ = $_GET['q'] ?? '';
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
    <form class="topbar-search" method="get" action="<?= $base ?>index.php">
      <span class="search-icon">🔍</span>
      <input type="text" name="q" value="<?= e($searchQ) ?>" placeholder="Cari" aria-label="Cari postingan atau pengguna">
    </form>
    <nav class="nav">
      <?php if ($user): ?>
        <a href="<?= $base ?>index.php" title="Beranda"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg></a>
        <a href="<?= $base ?>index.php" title="Jelajahi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></a>
        <a href="<?= $base ?>index.php#composer" title="Buat postingan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/></svg></a>
        <?php if ($user['role'] === 'admin'): ?>
          <a href="<?= $base ?>admin/index.php" class="nav-admin" title="Dashboard Admin">🛡️</a>
        <?php endif; ?>
        <a href="<?= $base ?>profile.php?u=<?= e($user['username']) ?>" title="Profil"><span class="avatar" style="width:28px;height:28px;font-size:12px"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></span></a>
        <a href="<?= $base ?>logout.php" title="Keluar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg></a>
      <?php else: ?>
        <a href="<?= $base ?>login.php" class="btn btn-primary btn-sm">Masuk</a>
        <a href="<?= $base ?>register.php">Daftar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
  <?php if ($flashMsg): ?>
    <div class="alert"><?= e($flashMsg) ?></div>
  <?php endif; ?>
