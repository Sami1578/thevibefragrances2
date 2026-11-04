<?php
require_once __DIR__ . '/_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    if ($act === 'add' && $name !== '') {
        db_exec("INSERT INTO categories (name, sort_order) VALUES (?, ?)", 'si', [$name, $order]);
        flash('Category added.');
    } elseif ($act === 'edit' && $id && $name !== '') {
        db_exec("UPDATE categories SET name = ?, sort_order = ? WHERE id = ?", 'sii', [$name, $order, $id]);
        flash('Category updated.');
    } elseif ($act === 'delete' && $id) {
        db_exec("UPDATE products SET category_id = NULL WHERE category_id = ?", 'i', [$id]);
        db_exec("DELETE FROM categories WHERE id = ?", 'i', [$id]);
        flash('Category deleted. Its products are now uncategorised.');
    }
    redirect('categories.php');
}

$adminTitle = 'Categories';
require __DIR__ . '/_header.php';
$rows = db_all("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS n FROM categories c ORDER BY sort_order, name");
?>
<form class="panel form inline-add" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="add">
  <label>New category<input name="name" required placeholder="e.g. Gift Sets"></label>
  <label>Order<input name="sort_order" type="number" value="0"></label>
  <button class="btn-a">Add</button>
</form>
<div class="panel">
<table>
  <tr><th>Name</th><th>Order</th><th>Products</th><th>Actions</th></tr>
  <?php foreach ($rows as $c): ?>
  <tr>
    <td>
      <form id="cf<?= (int)$c['id'] ?>" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"></form>
      <input class="cell" form="cf<?= (int)$c['id'] ?>" name="name" value="<?= e($c['name']) ?>">
    </td>
    <td><input class="cell s" form="cf<?= (int)$c['id'] ?>" name="sort_order" type="number" value="<?= (int)$c['sort_order'] ?>"></td>
    <td><?= (int)$c['n'] ?></td>
    <td class="actions">
      <button class="mini" form="cf<?= (int)$c['id'] ?>">Save</button>
      <form method="post" data-confirm="Delete this category?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="mini danger">Delete</button></form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
