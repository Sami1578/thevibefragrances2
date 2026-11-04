<?php
// One-click installer for TheVibe Fragrances (TiDB Cloud Compatible)
// Open this page once in your browser, then DELETE this file.
require_once __DIR__ . '/config.php';

$log = [];
$ok = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mysqli_report(MYSQLI_REPORT_OFF);
    
    // Initialize MySQLi client and enable SSL required by TiDB Cloud
    $c = mysqli_init();
    $c->ssl_set(NULL, NULL, NULL, NULL, NULL);
    
    // Connect using host, user, password, empty db, port, and SSL client flag
    $port = defined('DB_PORT') ? DB_PORT : 4000;
    $connected = @$c->real_connect(DB_HOST, DB_USER, DB_PASS, '', $port, NULL, MYSQLI_CLIENT_SSL);

    if (!$connected || $c->connect_errno) {
        $error = 'Cannot connect to TiDB Cloud: ' . $c->connect_error . '. Check DB_HOST / DB_USER / DB_PASS / DB_PORT in config.php.';
    } else {
        $c->set_charset('utf8mb4');
        $c->query('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        if (!$c->select_db(DB_NAME)) {
            $error = 'Could not create/select database: ' . $c->error;
        } else {
            $log[] = 'Database "' . DB_NAME . '" ready.';
            
            $sqlFile = __DIR__ . '/database.sql';
            if (!file_exists($sqlFile)) {
                $error = 'database.sql file not found in the directory.';
            } else {
                $sql = file_get_contents($sqlFile);
                if ($c->multi_query($sql)) {
                    do {
                        if ($res = $c->store_result()) {
                            $res->free();
                        }
                    } while ($c->more_results() && $c->next_result());
                }
                
                if ($c->errno) {
                    $error = 'SQL import error: ' . $c->error;
                } else {
                    $log[] = 'Tables and starter data created successfully.';
                    
                    $user = trim($_POST['username'] ?? 'admin') ?: 'admin';
                    $pass = $_POST['password'] ?? '';
                    if (strlen($pass) < 6) {
                        $pass = 'admin123';
                    }
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    
                    $st = $c->prepare('INSERT INTO admins (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)');
                    if ($st) {
                        $st->bind_param('ss', $user, $hash);
                        $st->execute();
                        $st->close();
                        $log[] = 'Admin account "' . htmlspecialchars($user) . '" saved.';
                    }
                    
                    $ok = true;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install – The Vibe Fragrances</title>
<style>
  body{margin:0;min-height:100vh;display:grid;place-items:center;background:#0b0b0d;color:#f3efe6;font-family:Segoe UI,Arial,sans-serif}
  .box{width:min(460px,92vw);background:#141418;border:1px solid #2a2a31;border-radius:16px;padding:34px}
  h1{font-family:Georgia,serif;margin:0 0 6px;color:#e3c370;font-weight:500}
  p{color:#9b978c;line-height:1.6;font-size:14px}
  label{display:block;font-size:13px;margin:16px 0 6px;color:#cfc9b8}
  input{width:100%;box-sizing:border-box;padding:12px 14px;border-radius:10px;border:1px solid #2f2f38;background:#0e0e11;color:#fff;font-size:15px}
  button,.btn{display:inline-block;margin-top:22px;width:100%;padding:13px;border:0;border-radius:10px;background:linear-gradient(135deg,#c9a24b,#f0d589);color:#17130a;font-weight:700;font-size:15px;cursor:pointer;text-align:center;text-decoration:none;box-sizing:border-box}
  .err{background:#3a1717;border:1px solid #7a2a2a;padding:12px;border-radius:10px;color:#ffb4b4;font-size:14px;margin-top:14px}
  .ok{background:#12301f;border:1px solid #1f6b3f;padding:12px 14px;border-radius:10px;color:#9be3b6;font-size:14px;margin-top:14px}
  .ok li{margin:4px 0}
</style>
</head>
<body>
<div class="box">
  <h1>The Vibe Fragrances</h1>
  <p>Installer – securely connects to TiDB Cloud, sets up tables, sample products, and your admin login.</p>
  <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($ok): ?>
    <div class="ok"><strong>Installation complete!</strong><ul><?php foreach ($log as $l) echo '<li>' . $l . '</li>'; ?></ul></div>
    <p><strong>Important:</strong> delete <code>install.php</code> from your server now.</p>
    <a class="btn" href="admin/login.php">Go to Admin Login</a>
    <a class="btn" style="background:#26262d;color:#f3efe6" href="index.php">View Website</a>
  <?php else: ?>
    <form method="post">
      <label>Admin username</label>
      <input name="username" value="admin" required>
      <label>Admin password (min 6 characters)</label>
      <input name="password" type="password" placeholder="admin123" >
      <button type="submit">Install Now</button>
    </form>
    <p style="margin-top:18px">Database: <code><?= htmlspecialchars(DB_NAME) ?></code> on TiDB Cloud port <code><?= htmlspecialchars(defined('DB_PORT') ? DB_PORT : 4000) ?></code>.</p>
  <?php endif; ?>
</div>
</body>
</html>