<?php $fu = currentUser(); ?>
</main>
<?php if ($fu): ?>
<nav class="bottom-nav" aria-label="Navigasi utama">
  <a href="<?= $base ?? '' ?>index.php" title="Beranda" class="bottom-nav-item active">
    <svg viewBox="0 0 24 24" width="28" height="28"><defs><linearGradient id="igHomeGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f09433"/><stop offset=".5" stop-color="#dc2743"/><stop offset="1" stop-color="#833ab4"/></linearGradient></defs><path fill="url(#igHomeGrad)" d="M12 2.5 2.8 10.2c-.3.3-.3.8 0 1.1.3.3.8.3 1.1 0L5 10.3V21c0 .6.4 1 1 1h4v-6h4v6h4c.6 0 1-.4 1-1v-10.7l1.1 1c.3.3.8.3 1.1 0 .3-.3.3-.8 0-1.1L12 2.5z"/></svg>
  </a>
  <a href="<?= $base ?? '' ?>index.php" title="Cari" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
  </a>
  <a href="<?= $base ?? '' ?>index.php#composer" title="Buat postingan" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/></svg>
  </a>
  <a href="<?= $base ?? '' ?>activity.php" title="Aktivitas" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21.2l7.8-7.8 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
  </a>
  <a href="<?= $base ?? '' ?>profile.php?u=<?= e($fu['username']) ?>" title="Profil" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg>
  </a>
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
