<?php
require_once __DIR__ . '/_auth.php';

$keys = ['site_name', 'tagline', 'announcement', 'whatsapp', 'phone', 'email', 'address', 'currency', 'delivery_fee', 'free_delivery_over', 'instagram', 'facebook'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? 'settings';
    if ($act === 'settings') {
        foreach ($keys as $k) {
            $val = trim($_POST[$k] ?? '');
            if ($k === 'whatsapp') {
                $val = preg_replace('/\D+/', '', $val);
            }
            db_exec("INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)", 'ss', [$k, $val]);
        }
        flash('Settings saved.');
    } elseif ($act === 'password') {
        $row = db_row("SELECT * FROM admins WHERE id = ?", 'i', [$_SESSION['admin_id']]);
        $cur = $_POST['current'] ?? '';
        $new = $_POST['new'] ?? '';
        if (!$row || !password_verify($cur, $row['password'])) {
            flash('Current password is incorrect.', 'err');
        } elseif (strlen($new) < 6) {
            flash('New password must be at least 6 characters.', 'err');
        } elseif ($new !== ($_POST['confirm'] ?? '')) {
            flash('New passwords do not match.', 'err');
        } else {
            db_exec("UPDATE admins SET password = ? WHERE id = ?", 'si', [password_hash($new, PASSWORD_DEFAULT), $row['id']]);
            flash('Password changed.');
        }
    }
    redirect('settings.php');
}

$adminTitle = 'Settings';
require __DIR__ . '/_header.php';
$S = fn($k) => e(setting($k));
?>
<form class="panel form" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="settings">
  <h2>Website</h2>
  <div class="two">
    <label>Site name<input name="site_name" value="<?= $S('site_name') ?>"></label>
    <label>Tagline<input name="tagline" value="<?= $S('tagline') ?>"></label>
  </div>
  <label>Top announcement bar (leave empty to hide)<input name="announcement" value="<?= $S('announcement') ?>"></label>

  <h2>Contact &amp; WhatsApp</h2>
  <div class="two">
    <label>WhatsApp number (with country code, digits only)<input name="whatsapp" value="<?= $S('whatsapp') ?>" placeholder="923001234567"></label>
    <label>Phone (display)<input name="phone" value="<?= $S('phone') ?>"></label>
  </div>
  <div class="two">
    <label>Email<input name="email" value="<?= $S('email') ?>"></label>
    <label>Address<input name="address" value="<?= $S('address') ?>"></label>
  </div>
  <div class="two">
    <label>Instagram URL<input name="instagram" value="<?= $S('instagram') ?>" placeholder="https://instagram.com/…"></label>
    <label>Facebook URL<input name="facebook" value="<?= $S('facebook') ?>" placeholder="https://facebook.com/…"></label>
  </div>

  <h2>Pricing &amp; Delivery</h2>
  <div class="three">
    <label>Currency symbol<input name="currency" value="<?= $S('currency') ?>"></label>
    <label>Delivery fee<input name="delivery_fee" type="number" min="0" value="<?= $S('delivery_fee') ?>"></label>
    <label>Free delivery over (0 = never)<input name="free_delivery_over" type="number" min="0" value="<?= $S('free_delivery_over') ?>"></label>
  </div>
  <div class="form-actions"><button class="btn-a">Save Settings</button></div>
</form>

<form class="panel form" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="password">
  <h2>Change Password</h2>
  <div class="three">
    <label>Current password<input name="current" type="password" required autocomplete="current-password"></label>
    <label>New password<input name="new" type="password" required minlength="6" autocomplete="new-password"></label>
    <label>Confirm new password<input name="confirm" type="password" required autocomplete="new-password"></label>
  </div>
  <div class="form-actions"><button class="btn-a ghost">Update Password</button></div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
