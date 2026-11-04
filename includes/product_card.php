<?php /* expects $p */ ?>
<article class="card">
  <a class="card-img" href="<?= url('product.php?id=' . (int)$p['id']) ?>">
    <?= product_img($p) ?>
    <?php if (on_sale($p)): ?><span class="badge">Sale</span><?php endif; ?>
    <?php if (!empty($p['size'])): ?><span class="size"><?= e($p['size']) ?></span><?php endif; ?>
  </a>
  <div class="card-body">
    <?php if (!empty($p['cat'])): ?><span class="card-cat"><?= e($p['cat']) ?></span><?php endif; ?>
    <h3><a href="<?= url('product.php?id=' . (int)$p['id']) ?>"><?= e($p['name']) ?></a></h3>
    <?php if (!empty($p['notes'])): ?><p class="card-notes"><?= e($p['notes']) ?></p><?php endif; ?>
    <div class="card-foot">
      <div class="price">
        <strong><?= money(effective_price($p)) ?></strong>
        <?php if (on_sale($p)): ?><del><?= money($p['price']) ?></del><?php endif; ?>
      </div>
      <button class="add-btn" data-add="<?= (int)$p['id'] ?>" aria-label="Add to cart">+ Add</button>
    </div>
  </div>
</article>
