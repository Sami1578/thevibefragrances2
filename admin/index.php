<?php
$adminTitle = 'Dashboard';
require __DIR__ . '/_header.php';

$stats = [
    'Products' => (int)db_row("SELECT COUNT(*) n FROM products")['n'],
    'Orders' => (int)db_row("SELECT COUNT(*) n FROM orders")['n'],
    'Pending' => (int)db_row("SELECT COUNT(*) n FROM orders WHERE status='pending'")['n'],
    'Revenue' => money(db_row("SELECT COALESCE(SUM(total),0) s FROM orders WHERE status <> 'cancelled'")['s']),
];
$recent = db_all("SELECT * FROM orders ORDER BY id DESC LIMIT 8");
?>
<div class="stats">
  <?php foreach ($stats as $l => $v): ?>
    <div class="stat"><small><?= $l ?></small><b><?= $v ?></b></div>
  <?php endforeach; ?>
</div>

<div class="quick">
  <a class="btn-a" href="product-form.php">+ Add Fragrance</a>
  <a class="btn-a ghost" href="banner-form.php">+ Add Banner</a>
  <a class="btn-a ghost" href="orders.php">View Orders</a>
</div>

<div class="panel">
  <h2>Recent Orders</h2>
  <div class="table-wrap">
  <table>
    <tr><th>Order</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
    <?php foreach ($recent as $o): ?>
      <tr>
        <td><b><?= e($o['order_ref']) ?></b></td>
        <td><?= e($o['customer_name']) ?></td>
        <td><?= e($o['phone']) ?></td>
        <td><?= money($o['total']) ?></td>
        <td><span class="pill <?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
        <td><?= date('d M, H:i', strtotime($o['created_at'])) ?></td>
        <td><a href="order-view.php?id=<?= (int)$o['id'] ?>">View</a></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$recent): ?><tr><td colspan="7" class="muted">No orders yet.</td></tr><?php endif; ?>
  </table>
  </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
