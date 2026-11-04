<?php
require_once __DIR__ . '/_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($act === 'delete' && $id) {
        $p = db_row("SELECT image FROM products WHERE id = ?", 'i', [$id]);
        if ($p) {
            delete_file($p['image']);
            foreach (db_all("SELECT image FROM product_images WHERE product_id = ?", 'i', [$id]) as $g) {
                delete_file($g['image']);
            }
            db_exec("DELETE FROM product_images WHERE product_id = ?", 'i', [$id]);
            db_exec("DELETE FROM products WHERE id = ?", 'i', [$id]);
            flash('Product deleted.');
        }
    } elseif ($act === 'toggle' && $id) {
        db_exec("UPDATE products SET is_active = 1 - is_active WHERE id = ?", 'i', [$id]);
        flash('Visibility updated.');
    } elseif ($act === 'feature' && $id) {
        db_exec("UPDATE products SET is_featured = 1 - is_featured WHERE id = ?", 'i', [$id]);
        flash('Featured status updated.');
    }
    redirect('products.php');
}

$adminTitle = 'Products';
require __DIR__ . '/_header.php';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $rows = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.name LIKE ? ORDER BY p.id DESC", 's', ['%' . $q . '%']);
} else {
    $rows = db_all("SELECT p.*, c.name AS cat FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC");
}
?>
<div class="bar">
  <form class="inline" method="get"><input name="q" placeholder="Search products…" value="<?= e($q) ?>"><button class="btn-a ghost">Search</button></form>
  <a class="btn-a" href="product-form.php">+ Add Fragrance</a>
</div>
<div class="panel">
<div class="table-wrap">
<table>
  <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Featured</th><th>Status</th><th>Actions</th></tr>
  <?php foreach ($rows as $p): ?>
  <tr>
    <td><div class="thumb"><?= $p['image'] ? '<img src="' . e(img_url($p['image'])) . '" alt="">' : '<span>—</span>' ?></div></td>
    <td><b><?= e($p['name']) ?></b><br><small class="muted"><?= e($p['size']) ?></small></td>
    <td><?= e($p['cat'] ?: '—') ?></td>
    <td><?= money(effective_price($p)) ?><?php if (on_sale($p)): ?><br><del class="muted"><?= money($p['price']) ?></del><?php endif; ?></td>
    <td>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="feature"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <button class="pill <?= $p['is_featured'] ? 'delivered' : 'cancelled' ?>"><?= $p['is_featured'] ? '★ Yes' : 'No' ?></button></form>
    </td>
    <td>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <button class="pill <?= $p['is_active'] ? 'delivered' : 'cancelled' ?>"><?= $p['is_active'] ? 'Visible' : 'Hidden' ?></button></form>
    </td>
    <td class="actions">
      <a class="mini" href="product-form.php?id=<?= (int)$p['id'] ?>">Edit</a>
      <form method="post" data-confirm="Delete this product permanently?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="mini danger">Delete</button></form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="7" class="muted">No products yet. Click “Add Fragrance”.</td></tr><?php endif; ?>
</table>
</div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
