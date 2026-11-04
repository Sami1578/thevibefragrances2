<?php
$adminTitle = 'Orders';
require __DIR__ . '/_header.php';
$status = $_GET['status'] ?? '';
$valid = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
if (in_array($status, $valid, true)) {
    $rows = db_all("SELECT * FROM orders WHERE status = ? ORDER BY id DESC", 's', [$status]);
} else {
    $status = '';
    $rows = db_all("SELECT * FROM orders ORDER BY id DESC");
}
?>
<div class="chips-a">
  <a class="<?= $status === '' ? 'on' : '' ?>" href="orders.php">All</a>
  <?php foreach ($valid as $s): ?><a class="<?= $status === $s ? 'on' : '' ?>" href="orders.php?status=<?= $s ?>"><?= ucfirst($s) ?></a><?php endforeach; ?>
</div>
<div class="panel">
<div class="table-wrap">
<table>
  <tr><th>Order</th><th>Customer</th><th>Phone</th><th>Address</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
  <?php foreach ($rows as $o): ?>
  <tr>
    <td><b><?= e($o['order_ref']) ?></b></td>
    <td><?= e($o['customer_name']) ?></td>
    <td><a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></td>
    <td class="addr"><?= e(mb_strimwidth($o['address'], 0, 50, '…')) ?></td>
    <td><?= money($o['total']) ?></td>
    <td><span class="pill <?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
    <td><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></td>
    <td><a class="mini" href="order-view.php?id=<?= (int)$o['id'] ?>">Open</a></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="8" class="muted">No orders found.</td></tr><?php endif; ?>
</table>
</div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
