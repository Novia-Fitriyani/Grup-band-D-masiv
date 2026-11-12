<?php
require_once __DIR__ . '/config/koneksi.php';

if (!empty($_SESSION['id_admin'])) {
    redirect('/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $q = $pdo->prepare("SELECT id_admin, nama_lengkap, password FROM admin WHERE username = ? AND status = 'aktif'");
    $q->execute([$username]);
    $admin = $q->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['id_admin'] = $admin['id_admin'];
        $_SESSION['nama']     = $admin['nama_lengkap'];
        redirect('/index.php');
    }
    $error = 'Username atau kata sandi salah, atau akun tidak aktif.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk | D'MASIV Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.10.0/dist/css/adminlte.min.css">
  
</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center"><span class="h1 fw-light">D'MASIV Admin</span></div>
    <div class="card-body login-card-body">
      <p class="login-box-msg">Masuk untuk mengelola konten website</p>
      <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post" autocomplete="off">
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
          <div class="input-group-text"><span class="bi bi-person"></span></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Kata sandi" required>
          <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
        </div>
        <div class="d-grid"><button type="submit" class="btn btn-primary">Masuk</button></div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.10.0/dist/js/adminlte.min.js"></script>
</body>
</html>
