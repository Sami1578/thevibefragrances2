<?php
require_once __DIR__ . '/includes/functions.php';

$items = cart_items();
if (!$items) {
    redirect(url('cart.php'));
}
$t = cart_totals($items);
$errors = [];
$old = ['name' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $old['name'] = trim($_POST['name'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
    $old['address'] = trim($_POST['address'] ?? '');

    if (mb_strlen($old['name']) < 2) {
        $errors[] = 'Please enter your full name.';
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $old['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if (mb_strlen($old['address']) < 8) {
        $errors[] = 'Please enter your complete delivery address.';
    }

    if (!$errors) {
        $token = bin2hex(random_bytes(16));
        $oid = db_exec(
            "INSERT INTO orders (token, customer_name, phone, address, subtotal, delivery_fee, total) VALUES (?,?,?,?,?,?,?)",
            'ssssddd',
            [$token, $old['name'], $old['phone'], $old['address'], $t['subtotal'], $t['delivery'], $t['total']]
        );
        $ref = 'TV' . str_pad((string)$oid, 5, '0', STR_PAD_LEFT);
        db_exec("UPDATE orders SET order_ref = ? WHERE id = ?", 'si', [$ref, $oid]);
        foreach ($items as $it) {
            db_exec(
                "INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?,?,?,?,?)",
                'iisdi',
                [$oid, $it['id'], $it['name'], $it['unit'], $it['qty']]
            );
        }
        $_SESSION['cart'] = [];
        redirect(url('order-success.php?id=' . $oid . '&t=' . $token));
    }
}

$pageTitle = 'Checkout';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero small"><div class="container"><h1>Checkout</h1></div></section>

<section class="container section-tight">
  <div class="cart-grid">
    <form class="checkout-form" method="post" novalidate>
      <?= csrf_field() ?>
      <h3>Delivery Details</h3>
      <?php if ($errors): ?>
        <div class="alert err"><?php foreach ($errors as $er) echo '<div>' . e($er) . '</div>'; ?></div>
      <?php endif; ?>
      <label>Full Name
        <input name="name" value="<?= e($old['name']) ?>" placeholder="Your name" required autocomplete="name">
      </label>
      <label>Phone Number
        <input name="phone" type="tel" value="<?= e($old['phone']) ?>" placeholder="03XX XXXXXXX" required autocomplete="tel">
      </label>
      <label>Delivery Address
        <textarea name="address" rows="4" placeholder="House #, street, area, city" required autocomplete="street-address"><?= e($old['address']) ?></textarea>
      </label>
      <div class="pay-note">💳 <b>Cash on Delivery</b> – pay when your order arrives.</div>
      <button class="btn btn-gold block" type="submit">Place Order · <?= money($t['total']) ?></button>
    </form>

    <aside class="summary">
      <h3>Your Order</h3>
      <?php foreach ($items as $it): ?>
        <div class="mini-row">
          <div class="mini-img"><?= product_img($it) ?></div>
          <div class="mini-info"><b><?= e($it['name']) ?></b><small>Qty <?= (int)$it['qty'] ?></small></div>
          <span><?= money($it['line']) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="sum-row"><span>Subtotal</span><b><?= money($t['subtotal']) ?></b></div>
      <div class="sum-row"><span>Delivery</span><b><?= $t['delivery'] > 0 ? money($t['delivery']) : 'FREE' ?></b></div>
      <div class="sum-row total"><span>Total</span><b><?= money($t['total']) ?></b></div>
      <a class="back-link" href="<?= url('cart.php') ?>">← Edit cart</a>
    </aside>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
