</main>

<footer class="site-footer">
  <div class="container foot-grid">
    <div>
      <a class="logo" href="<?= url('index.php') ?>"><span class="logo-main">THE VIBE</span><span class="logo-sub">FRAGRANCES</span></a>
      <p class="foot-text"><?= e(setting('tagline')) ?> Premium perfumes, delivered across the country with love.</p>
      <div class="socials">
        <?php if (setting('instagram')): ?><a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
        <?php if (setting('facebook')): ?><a href="<?= e(setting('facebook')) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
        <a href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">WhatsApp</a>
      </div>
    </div>
    <div>
      <h4>Shop</h4>
      <a href="<?= url('shop.php') ?>">All Fragrances</a>
      <?php foreach ($cats as $c): ?>
        <a href="<?= url('shop.php?cat=' . $c['id']) ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
      <a href="<?= url('shop.php?sort=new') ?>">New Arrivals</a>
    </div>
    <div>
      <h4>Customer Care</h4>
      <a href="<?= url('cart.php') ?>">My Cart</a>
      <a href="<?= e(wa_link('Hello! I need help with my order.')) ?>" target="_blank" rel="noopener">Order Support</a>
      <span>Cash on delivery</span>
      <span>Fast nationwide shipping</span>
    </div>
    <div>
      <h4>Contact</h4>
      <span><?= e(setting('phone')) ?></span>
      <span><?= e(setting('email')) ?></span>
      <span><?= e(setting('address')) ?></span>
    </div>
  </div>
  <div class="container foot-bottom">© <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.</div>
</footer>

<a class="wa-float" href="<?= e(wa_link('Hello! I would like to order a fragrance from ' . $siteName . '.')) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 32 32" width="32" height="32" fill="#fff"><path d="M16.04 3C8.84 3 3 8.83 3 16.02c0 2.3.6 4.54 1.74 6.52L3 29l6.63-1.7a13 13 0 006.4 1.64h.01C23.23 28.94 29 23.1 29 15.9 29 8.7 23.24 3 16.04 3zm0 23.7h-.01a10.7 10.7 0 01-5.45-1.5l-.39-.23-3.93 1.01 1.05-3.83-.25-.4a10.65 10.65 0 01-1.64-5.7c0-5.9 4.8-10.7 10.7-10.7 2.86 0 5.54 1.11 7.56 3.13a10.62 10.62 0 013.13 7.57c0 5.9-4.8 10.65-10.77 10.65zm5.9-8c-.32-.16-1.9-.94-2.2-1.05-.3-.1-.5-.16-.72.16-.22.32-.83 1.05-1.02 1.27-.19.22-.38.24-.7.08-.32-.16-1.36-.5-2.6-1.6-.96-.86-1.6-1.9-1.8-2.22-.19-.32-.02-.5.14-.66.14-.14.32-.38.48-.57.16-.19.22-.32.32-.54.1-.22.05-.4-.03-.57-.08-.16-.72-1.74-.99-2.38-.26-.62-.52-.54-.72-.55h-.62c-.22 0-.57.08-.86.4-.3.32-1.13 1.1-1.13 2.7s1.16 3.13 1.32 3.35c.16.22 2.28 3.48 5.52 4.88.77.33 1.37.53 1.84.68.77.25 1.48.21 2.04.13.62-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.3-.21-.62-.37z"/></svg>
  <span class="wa-tip">Chat with us</span>
</a>

<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
