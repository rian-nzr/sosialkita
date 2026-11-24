<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) redirect('index.php');
$pageTitle = 'Daftar';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($name) < 3) $error = 'Nama minimal 3 karakter.';
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) $error = 'Username 3–20 karakter, huruf/angka/underscore saja.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Email tidak valid.';
    elseif (strlen($password) < 6) $error = 'Password minimal 6 karakter.';
    else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) $error = 'Username atau email sudah dipakai.';
        else {
            $stmt = $pdo->prepare('INSERT INTO users (name, username, email, password) VALUES (?,?,?,?)');
            $stmt->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            flash('Selamat datang di SosialKita, ' . $name . '! 🎉');
            redirect('index.php');
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="card">
    <div class="logo"><span class="logo-mark">◉</span> SosialKita</div>
    <p class="muted">Daftar gratis, langsung bisa posting.</p>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Nama lengkap</label>
      <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="cth: Rani Putri">
      <label>Username</label>
      <input type="text" name="username" required value="<?= e($_POST['username'] ?? '') ?>" placeholder="cth: rani">
      <label>Email</label>
      <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="nama@email.com">
      <label>Password</label>
      <input type="password" name="password" required placeholder="Minimal 6 karakter">
      <button class="btn btn-primary btn-block" type="submit">Daftar</button>
    </form>
    <p style="margin-top:14px;text-align:center">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
