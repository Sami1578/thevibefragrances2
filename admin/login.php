<?php
require_once __DIR__ . '/../includes/functions.php';

// First run safety net: create a default admin if none exists (admin / admin123)
$count = (int)(db_row("SELECT COUNT(*) AS n FROM admins")['n'] ?? 0);
if ($count === 0) {
    db_exec("INSERT INTO admins (username, password) VALUES (?, ?)", 'ss', ['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
}

if (!empty($_SESSION['admin_id'])) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $row = db_row("SELECT * FROM admins WHERE username = ?", 's', [$u]);
    if ($row && password_verify($p, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$row['id'];
        $_SESSION['admin_name'] = $row['username'];
        redirect('index.php');
    }
    $error = 'Invalid username or password.';
    usleep(400000);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login – <?= e(setting('site_name')) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body class="login-body">
  <form class="login-card" method="post">
    <?= csrf_field() ?>
    <div class="brand big">THE VIBE<small>FRAGRANCES · ADMIN</small></div>
    <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
    <label>Username<input name="username" required autofocus autocomplete="username"></label>
    <label>Password<input name="password" type="password" required autocomplete="current-password"></label>
    <button class="btn-a" type="submit">Sign in</button>
    <a class="muted-link" href="<?= url('index.php') ?>">← Back to website</a>
  </form>
</body>
</html>
