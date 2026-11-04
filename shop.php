<?php
$pageTitle = 'Shop';
require __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$sort = $_GET['sort'] ?? 'new';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 12;

$where = 'p.is_active = 1';
$types = '';
$params = [];
if ($q !== '') {
    $where .= ' AND (p.name LIKE ? OR p.description LIKE ? OR p.notes LIKE ?)';
    $like = '%' . $q . '%';
    $types .= 'sss';
    array_push($params, $like, $like, $like);
}
if ($cat) {
    $where .= ' AND p.category_id = ?';
    $types .= 'i';
    $params[] = $cat;
}
$order = ['new' => 'p.id DESC', 'low' => 'p.price ASC', 'high' => 'p.price DESC', 'name' => 'p.name ASC'][$sort] ?? 'p.id DESC';

$total = (int)(db_row("SELECT COUNT(*) AS n FROM products p WHERE $where", $types, $params)['n'] ?? 0);
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;
$products = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE $where ORDER BY $order LIMIT $per OFFSET $offset", $types, $params);

$activeCat = $cat ? db_row("SELECT name FROM categories WHERE id = ?", 'i', [$cat]) : null;

function shop_url($over = [])
{
    $base = ['q' => $_GET['q'] ?? '', 'cat' => $_GET['cat'] ?? '', 'sort' => $_GET['sort'] ?? '', 'page' => ''];
    $m = array_filter(array_merge($base, $over), fn($v) => $v !== '' && $v !== null && $v !== 0);
    return url('shop.php') . ($m ? '?' . http_build_query($m) : '');
}
?>
<section class="page-hero">
  <div class="container">
    <span class="eyebrow">The Collection</span>
    <h1><?= $activeCat ? e($activeCat['name']) : ($q !== '' ? 'Results for “' . e($q) . '”' : 'All Fragrances') ?></h1>
    <p><?= $total ?> fragrance<?= $total === 1 ? '' : 's' ?> found</p>
  </div>
</section>

<section class="container section-tight">
  <div class="filters">
    <div class="chips">
      <a class="chip <?= !$cat ? 'on' : '' ?>" href="<?= shop_url(['cat' => '', 'page' => '']) ?>">All</a>
      <?php foreach ($cats as $c): ?>
        <a class="chip <?= $cat === (int)$c['id'] ? 'on' : '' ?>" href="<?= shop_url(['cat' => $c['id'], 'page' => '']) ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <select class="sort" onchange="location.href=this.value">
      <?php foreach (['new' => 'Newest', 'low' => 'Price: Low to High', 'high' => 'Price: High to Low', 'name' => 'Name A–Z'] as $k => $l): ?>
        <option value="<?= shop_url(['sort' => $k, 'page' => '']) ?>" <?= $sort === $k ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php if ($products): ?>
    <div class="grid">
      <?php foreach ($products as $p) { include __DIR__ . '/includes/product_card.php'; } ?>
    </div>
    <?php if ($pages > 1): ?>
      <div class="pager">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a class="<?= $i === $page ? 'on' : '' ?>" href="<?= shop_url(['page' => $i > 1 ? $i : '']) ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="empty"><h3>No fragrances found</h3><p>Try a different search or category.</p><a class="btn btn-gold" href="<?= url('shop.php') ?>">Clear filters</a></div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
