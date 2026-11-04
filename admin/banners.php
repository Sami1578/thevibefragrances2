<?php
require_once __DIR__ . '/_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['action'] ?? '';
    if ($act === 'delete' && $id) {
        $b = db_row("SELECT image FROM banners WHERE id = ?", 'i', [$id]);
        if ($b) {
            delete_file($b['image']);
            db_exec("DELETE FROM banners WHERE id = ?", 'i', [$id]);
            flash('Banner deleted.');
        }
    } elseif ($act === 'toggle' && $id) {
        db_exec("UPDATE banners SET is_active = 1 - is_active WHERE id = ?", 'i', [$id]);
        flash('Banner updated.');
    }
    redirect('banners.php');
}

$adminTitle = 'Banners';
require __DIR__ . '/_header.php';
$rows = db_all("SELECT * FROM banners ORDER BY sort_order, id");
?>
<div class="bar"><p class="muted">Banners appear in the home page slider. Upload wide images (about 1920×800) for the best look.</p><a class="btn-a" href="banner-form.php">+ Add Banner</a></div>
<div class="panel">
<div class="table-wrap">
<table>
  <tr><th>Image</th><th>Title</th><th>Button</th><th>Order</th><th>Status</th><th>Actions</th></tr>
  <?php foreach ($rows as $b): ?>
  <tr>
    <td><div class="thumb wide"><?= $b['image'] ? '<img src="' . e(img_url($b['image'])) . '" alt="">' : '<span>gradient</span>' ?></div></td>
    <td><b><?= e($b['title']) ?></b><br><small class="muted"><?= e($b['subtitle']) ?></small></td>
    <td><?= e($b['button_text']) ?><br><small class="muted"><?= e($b['button_link']) ?></small></td>
    <td><?= (int)$b['sort_order'] ?></td>
    <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
      <button class="pill <?= $b['is_active'] ? 'delivered' : 'cancelled' ?>"><?= $b['is_active'] ? 'Active' : 'Hidden' ?></button></form></td>
    <td class="actions">
      <a class="mini" href="banner-form.php?id=<?= (int)$b['id'] ?>">Edit</a>
      <form method="post" data-confirm="Delete this banner?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="mini danger">Delete</button></form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="muted">No banners yet.</td></tr><?php endif; ?>
</table>
</div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
