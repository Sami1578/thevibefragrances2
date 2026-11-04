<?php
$pageTitle = null;
require __DIR__ . '/includes/header.php';

$banners = db_all("SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order, id");
$featured = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.id DESC LIMIT 8");
$newest = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1 ORDER BY p.id DESC LIMIT 8");
?>

<!-- HERO / BANNERS -->
<section class="hero" id="hero">
  <?php foreach ($banners as $i => $b): ?>
    <div class="slide <?= $i === 0 ? 'active' : '' ?> g<?= ($i % 3) + 1 ?>" <?= $b['image'] ? 'style="background-image:url(\'' . e(img_url($b['image'])) . '\')"' : '' ?>>
      <div class="slide-overlay"></div>
      <div class="container slide-inner">
        <span class="eyebrow">The Vibe Collection</span>
        <h1><?= e($b['title']) ?></h1>
        <?php if ($b['subtitle']): ?><p><?= e($b['subtitle']) ?></p><?php endif; ?>
        <?php if ($b['button_text']): ?>
          <a class="btn btn-gold" href="<?= e(preg_match('#^https?://#', (string)$b['button_link']) ? $b['button_link'] : url($b['button_link'] ?: 'shop.php')) ?>"><?= e($b['button_text']) ?> <span>→</span></a>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$banners): ?>
    <div class="slide active g1"><div class="slide-overlay"></div><div class="container slide-inner">
      <span class="eyebrow">The Vibe Collection</span><h1>Discover Your Signature Scent</h1>
      <a class="btn btn-gold" href="<?= url('shop.php') ?>">Shop Collection <span>→</span></a></div></div>
  <?php endif; ?>
  <?php if (count($banners) > 1): ?>
    <button class="hero-nav prev" id="heroPrev" aria-label="Previous">‹</button>
    <button class="hero-nav next" id="heroNext" aria-label="Next">›</button>
    <div class="dots" id="heroDots">
      <?php foreach ($banners as $i => $b): ?><button class="<?= $i === 0 ? 'on' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- PERKS MARQUEE -->
<div class="marquee"><div class="marquee-track">
  <?php for ($k = 0; $k < 2; $k++): ?>
    <span>✦ Long-lasting scents</span><span>✦ Authentic premium oils</span><span>✦ Cash on delivery</span><span>✦ Nationwide delivery</span><span>✦ Gift ready packaging</span><span>✦ Order via WhatsApp</span>
  <?php endfor; ?>
</div></div>

<!-- CATEGORIES -->
<?php if ($cats): ?>
<section class="section container reveal">
  <div class="sec-head"><span class="eyebrow">Browse</span><h2>Shop by Category</h2></div>
  <div class="cat-grid">
    <?php foreach ($cats as $i => $c): ?>
      <a class="cat-tile ct<?= ($i % 3) + 1 ?>" href="<?= url('shop.php?cat=' . $c['id']) ?>">
        <span class="cat-name"><?= e($c['name']) ?></span>
        <span class="cat-link">Explore →</span>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- FEATURED -->
<section class="section container reveal">
  <div class="sec-head"><span class="eyebrow">Hand-picked</span><h2>Featured Fragrances</h2><p>Our most-loved scents, chosen for unforgettable presence.</p></div>
  <div class="grid">
    <?php foreach ($featured ?: $newest as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
  <div class="center"><a class="btn btn-outline" href="<?= url('shop.php') ?>">View All Fragrances</a></div>
</section>

<!-- BRAND STORY -->
<section class="story reveal">
  <div class="container story-grid">
    <div class="story-art"><?= placeholder_svg() ?></div>
    <div class="story-text">
      <span class="eyebrow">Our Story</span>
      <h2>Scent is the most intimate form of style</h2>
      <p>At <?= e(setting('site_name')) ?>, every bottle is a story waiting to be worn. We blend rich oils and rare notes into fragrances that linger on your skin and in everyone's memory.</p>
      <ul class="ticks">
        <li>Premium, skin-friendly ingredients</li>
        <li>Luxury packaging, perfect for gifting</li>
        <li>Quick delivery with cash on delivery</li>
      </ul>
      <a class="btn btn-gold" href="<?= url('shop.php') ?>">Find Your Scent <span>→</span></a>
    </div>
  </div>
</section>

<!-- NEW ARRIVALS -->
<section class="section container reveal">
  <div class="sec-head"><span class="eyebrow">Just landed</span><h2>New Arrivals</h2></div>
  <div class="grid">
    <?php foreach (array_slice($newest, 0, 4) as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
  </div>
</section>

<!-- FEATURES -->
<section class="features container reveal">
  <div class="feat"><div class="ico">✦</div><h4>Authentic Quality</h4><p>Carefully sourced, beautifully blended.</p></div>
  <div class="feat"><div class="ico">🚚</div><h4>Fast Delivery</h4><p>Doorstep delivery nationwide.</p></div>
  <div class="feat"><div class="ico">💳</div><h4>Cash on Delivery</h4><p>Pay only when it arrives.</p></div>
  <div class="feat"><div class="ico">💬</div><h4>WhatsApp Support</h4><p>We reply fast, anytime.</p></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
