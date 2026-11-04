<?php
require_once __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$tok = $_GET['t'] ?? '';
$o = db_row("SELECT * FROM orders WHERE id = ? AND token = ?", 'is', [$id, $tok]);
if (!$o) {
    redirect(url('index.php'));
}
$items = db_all("SELECT * FROM order_items WHERE order_id = ?", 'i', [$id]);

$lines = ["New order {$o['order_ref']} from " . setting('site_name'), ''];
foreach ($items as $it) {
    $lines[] = "• {$it['product_name']} x{$it['qty']} – " . money($it['price'] * $it['qty']);
}
$lines[] = '';
$lines[] = 'Total: ' . money($o['total']);
$lines[] = "Name: {$o['customer_name']}";
$lines[] = "Phone: {$o['phone']}";
$lines[] = "Address: {$o['address']}";

$pageTitle = 'Order Confirmed';
require __DIR__ . '/includes/header.php';
?>
<section class="container success">
  <div class="success-card">
    <div class="check">✓</div>
    <h1>Thank you, <?= e(explode(' ', $o['customer_name'])[0]) ?>!</h1>
    <p>Your order <b><?= e($o['order_ref']) ?></b> has been placed. We will call or message you on <b><?= e($o['phone']) ?></b> to confirm delivery.</p>
    <div class="success-items">
      <?php foreach ($items as $it): ?>
        <div class="sum-row"><span><?= e($it['product_name']) ?> × <?= (int)$it['qty'] ?></span><b><?= money($it['price'] * $it['qty']) ?></b></div>
      <?php endforeach; ?>
      <div class="sum-row"><span>Delivery</span><b><?= $o['delivery_fee'] > 0 ? money($o['delivery_fee']) : 'FREE' ?></b></div>
      <div class="sum-row total"><span>Total (Cash on Delivery)</span><b><?= money($o['total']) ?></b></div>
    </div>
    <a class="btn btn-wa block" target="_blank" rel="noopener" href="<?= e(wa_link(implode("\n", $lines))) ?>">Confirm faster on WhatsApp</a>
    <a class="btn btn-outline block" href="<?= url('shop.php') ?>">Continue Shopping</a>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
