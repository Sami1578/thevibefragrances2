<?php
require_once __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$p = db_row("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ? AND p.is_active = 1", 'i', [$id]);
if (!$p) {
    http_response_code(404);
    $pageTitle = 'Not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container empty"><h3>Fragrance not found</h3><a class="btn btn-gold" href="' . url('shop.php') . '">Back to shop</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
$gallery = [];
if ($p['image']) {
    $gallery[] = $p['image'];
}
foreach (db_all("SELECT image FROM product_images WHERE product_id = ? ORDER BY id", 'i', [$id]) as $g) {
    $gallery[] = $g['image'];
}
$related = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND p.id <> ? ORDER BY (p.category_id <=> ?) DESC, p.id DESC LIMIT 4", 'ii', [$id, $p['category_id']]);

$pageTitle = $p['name'];
$metaDesc = mb_substr(strip_tags($p['description'] ?? ''), 0, 150);
require __DIR__ . '/includes/header.php';
?>
<section class="container pdp">
  <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('shop.php') ?>">Shop</a> / <span><?= e($p['name']) ?></span></div>
  <div class="pdp-grid">
    <div class="gallery">
      <div class="g-main" id="gMain">
        <?php if ($gallery): ?>
          <img id="gMainImg" src="<?= e(img_url($gallery[0])) ?>" alt="<?= e($p['name']) ?>">
        <?php else: ?>
          <div class="ph-wrap"><?= placeholder_svg() ?></div>
        <?php endif; ?>
        <?php if (on_sale($p)): ?><span class="badge">Sale</span><?php endif; ?>
      </div>
      <?php if (count($gallery) > 1): ?>
        <div class="g-thumbs">
          <?php foreach ($gallery as $i => $g): ?>
            <button class="<?= $i === 0 ? 'on' : '' ?>" data-src="<?= e(img_url($g)) ?>"><img src="<?= e(img_url($g)) ?>" alt=""></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="pdp-info">
      <?php if ($p['cat']): ?><span class="eyebrow"><?= e($p['cat']) ?></span><?php endif; ?>
      <h1><?= e($p['name']) ?></h1>
      <div class="pdp-price">
        <strong><?= money(effective_price($p)) ?></strong>
        <?php if (on_sale($p)): ?><del><?= money($p['price']) ?></del><span class="save">Save <?= money($p['price'] - effective_price($p)) ?></span><?php endif; ?>
      </div>
      <?php if ($p['size'] || $p['notes']): ?>
        <div class="meta-row">
          <?php if ($p['size']): ?><div><small>Size</small><b><?= e($p['size']) ?></b></div><?php endif; ?>
          <?php if ($p['notes']): ?><div><small>Notes</small><b><?= e($p['notes']) ?></b></div><?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="pdp-desc"><?= nl2br(e($p['description'])) ?></div>

      <div class="buy-row">
        <div class="qty" data-qty>
          <button type="button" data-dec>−</button>
          <input type="number" id="pdpQty" value="1" min="1" max="20">
          <button type="button" data-inc>+</button>
        </div>
        <button class="btn btn-gold grow" data-add="<?= (int)$p['id'] ?>" data-qty-input="#pdpQty">Add to Cart</button>
      </div>
      <a class="btn btn-wa" target="_blank" rel="noopener" href="<?= e(wa_link('Hello! I want to order: ' . $p['name'] . ' (' . money(effective_price($p)) . ')')) ?>">
        Order on WhatsApp
      </a>
      <ul class="trust">
        <li>🚚 Delivery nationwide</li>
        <li>💳 Cash on delivery</li>
        <li>🎁 Gift-ready packaging</li>
      </ul>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section container">
  <div class="sec-head"><span class="eyebrow">You may also love</span><h2>Related Fragrances</h2></div>
  <div class="grid">
    <?php foreach ($related as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
