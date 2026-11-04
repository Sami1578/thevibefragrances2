<?php
require_once __DIR__ . '/_auth.php';
$id = (int)($_GET['id'] ?? 0);
$o = db_row("SELECT * FROM orders WHERE id = ?", 'i', [$id]);
if (!$o) {
    redirect('orders.php');
}
$valid = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['action'] ?? '') === 'status' && in_array($_POST['status'] ?? '', $valid, true)) {
        db_exec("UPDATE orders SET status = ? WHERE id = ?", 'si', [$_POST['status'], $id]);
        flash('Order status updated.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        db_exec("DELETE FROM order_items WHERE order_id = ?", 'i', [$id]);
        db_exec("DELETE FROM orders WHERE id = ?", 'i', [$id]);
        flash('Order deleted.');
        redirect('orders.php');
    }
    redirect('order-view.php?id=' . $id);
}

$items = db_all("SELECT * FROM order_items WHERE order_id = ?", 'i', [$id]);
$adminTitle = 'Order ' . $o['order_ref'];
require __DIR__ . '/_header.php';

$customerNum = preg_replace('/\D+/', '', $o['phone']);
if (strpos($customerNum, '0') === 0) {
    $customerNum = '92' . substr($customerNum, 1);
}
?>
<div class="two-col">
  <div class="panel">
    <h2>Items</h2>
    <table>
      <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr>
      <?php foreach ($items as $it): ?>
        <tr><td><?= e($it['product_name']) ?></td><td><?= money($it['price']) ?></td><td><?= (int)$it['qty'] ?></td><td><?= money($it['price'] * $it['qty']) ?></td></tr>
      <?php endforeach; ?>
      <tr><td colspan="3" align="right">Subtotal</td><td><?= money($o['subtotal']) ?></td></tr>
      <tr><td colspan="3" align="right">Delivery</td><td><?= money($o['delivery_fee']) ?></td></tr>
      <tr><td colspan="3" align="right"><b>Total (COD)</b></td><td><b><?= money($o['total']) ?></b></td></tr>
    </table>
  </div>
  <div>
    <div class="panel">
      <h2>Customer</h2>
      <p><b><?= e($o['customer_name']) ?></b></p>
      <p><a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></p>
      <p class="muted" style="white-space:pre-line"><?= e($o['address']) ?></p>
      <p><small class="muted">Placed <?= date('d M Y, H:i', strtotime($o['created_at'])) ?></small></p>
      <a class="btn-a wa" target="_blank" rel="noopener" href="https://wa.me/<?= e($customerNum) ?>?text=<?= rawurlencode('Hello ' . $o['customer_name'] . ', this is ' . setting('site_name') . ' regarding your order ' . $o['order_ref'] . '.') ?>">Message on WhatsApp</a>
    </div>
    <form class="panel form" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="status">
      <h2>Status</h2>
      <select name="status">
        <?php foreach ($valid as $s): ?><option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
      </select>
      <div class="form-actions"><button class="btn-a">Update Status</button></div>
    </form>
    <form method="post" data-confirm="Delete this order permanently?">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <button class="mini danger">Delete order</button>
    </form>
  </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
