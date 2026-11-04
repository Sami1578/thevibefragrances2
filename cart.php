<?php
$pageTitle = 'Your Cart';
require __DIR__ . '/includes/header.php';
$items = cart_items();
$t = cart_totals($items);
$freeOver = (float)setting('free_delivery_over', 0);
?>
<section class="page-hero small"><div class="container"><h1>Shopping Cart</h1></div></section>

<section class="container section-tight">
<?php if (!$items): ?>
  <div class="empty">
    <div class="empty-ico">🛍️</div>
    <h3>Your cart is empty</h3>
    <p>Discover a fragrance you will love.</p>
    <a class="btn btn-gold" href="<?= url('shop.php') ?>">Start Shopping</a>
  </div>
<?php else: ?>
  <div class="cart-grid">
    <div class="cart-list">
      <?php foreach ($items as $it): ?>
        <div class="cart-row">
          <a class="cr-img" href="<?= url('product.php?id=' . (int)$it['id']) ?>"><?= product_img($it) ?></a>
          <div class="cr-info">
            <a href="<?= url('product.php?id=' . (int)$it['id']) ?>"><h4><?= e($it['name']) ?></h4></a>
            <small><?= e($it['size']) ?></small>
            <div class="cr-unit"><?= money($it['unit']) ?></div>
          </div>
          <form method="post" action="<?= url('cart_action.php') ?>" class="qty-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
            <div class="qty" data-qty>
              <button type="button" data-dec>−</button>
              <input type="number" name="qty" value="<?= (int)$it['qty'] ?>" min="1" max="20" data-autosubmit>
              <button type="button" data-inc>+</button>
            </div>
          </form>
          <div class="cr-line"><?= money($it['line']) ?></div>
          <form method="post" action="<?= url('cart_action.php') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
            <button class="cr-x" aria-label="Remove">✕</button>
          </form>
        </div>
      <?php endforeach; ?>
      <a class="back-link" href="<?= url('shop.php') ?>">← Continue shopping</a>
    </div>

    <aside class="summary">
      <h3>Order Summary</h3>
      <div class="sum-row"><span>Subtotal</span><b><?= money($t['subtotal']) ?></b></div>
      <div class="sum-row"><span>Delivery</span><b><?= $t['delivery'] > 0 ? money($t['delivery']) : 'FREE' ?></b></div>
      <?php if ($freeOver > 0 && $t['subtotal'] < $freeOver): ?>
        <div class="free-note">Add <?= money($freeOver - $t['subtotal']) ?> more for free delivery</div>
      <?php endif; ?>
      <div class="sum-row total"><span>Total</span><b><?= money($t['total']) ?></b></div>
      <a class="btn btn-gold block" href="<?= url('checkout.php') ?>">Proceed to Checkout</a>
      <p class="sum-note">💳 Cash on delivery available</p>
    </aside>
  </div>
<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
