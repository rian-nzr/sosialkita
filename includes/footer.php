<?php $fu = currentUser(); ?>
</main>
<?php if ($fu): ?>
<nav class="bottom-nav">
  <a href="<?= $base ?? '' ?>index.php" title="Beranda"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg></a>
  <a href="<?= $base ?? '' ?>index.php" title="Jelajahi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></a>
  <a href="<?= $base ?? '' ?>index.php#composer" title="Buat postingan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/></svg></a>
  <?php if ($fu['role'] === 'admin'): ?>
  <a href="<?= $base ?? '' ?>admin/index.php" title="Admin">🛡️</a>
  <?php endif; ?>
  <a href="<?= $base ?? '' ?>profile.php?u=<?= e($fu['username']) ?>" title="Profil"><span class="avatar" style="width:28px;height:28px;font-size:12px"><?= e(strtoupper(substr($fu['name'], 0, 1))) ?></span></a>
</nav>
<?php endif; ?>
<footer class="footer">
  <div class="container">
    <p>© <?= date('Y') ?> <?= e(APP_NAME) ?> — Proyek Akhir: Website Sosial Media (PHP + MySQL)</p>
    <p class="muted">Posting teks &amp; foto • Komentar • Like • Dashboard Admin</p>
  </div>
</footer>
</body>
</html>
