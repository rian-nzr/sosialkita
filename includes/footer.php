<?php $fu = currentUser(); ?>
</main>
<?php if ($fu): ?>
<nav class="bottom-nav">
  <a href="<?= $base ?? '' ?>index.php" title="Beranda"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg></a>
  <a href="<?= $base ?? '' ?>profile.php?u=<?= e($fu['username']) ?>" title="Profil"><span class="avatar" style="width:28px;height:28px;font-size:12px"><?= e(strtoupper(substr($fu['name'], 0, 1))) ?></span></a>
  <?php if ($fu['role'] === 'admin'): ?>
  <a href="<?= $base ?? '' ?>admin/index.php" title="Admin">🛡️</a>
  <?php endif; ?>
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
