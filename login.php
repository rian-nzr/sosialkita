<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) redirect('index.php');
$pageTitle = 'Masuk';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['user_id'] = (int) $u['id'];
        flash('Selamat datang kembali, ' . $u['name'] . '!');
        redirect($u['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    $error = 'Email atau password salah.';
}
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="card">
    <div class="logo"><span class="logo-mark">◉</span> Forum JMK48</div>
    <p class="muted">Masuk untuk posting, like, dan komentar.</p>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="nama@email.com">
      <label>Password</label>
      <input type="password" name="password" required placeholder="Password kamu">
      <button class="btn btn-primary btn-block" type="submit">Masuk</button>
    </form>
    <p style="margin-top:14px;text-align:center">Belum punya akun? <a href="register.php">Daftar dulu</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
