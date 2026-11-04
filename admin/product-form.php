<?php
require_once __DIR__ . '/_auth.php';

$id = (int)($_GET['id'] ?? 0);
$p = $id ? db_row("SELECT * FROM products WHERE id = ?", 'i', [$id]) : null;
if ($id && !$p) {
    redirect('products.php');
}
$cats = db_all("SELECT * FROM categories ORDER BY sort_order, name");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $saleRaw = trim($_POST['sale_price'] ?? '');
    $sale = $saleRaw !== '' ? (float)$saleRaw : null;
    $catId = (int)($_POST['category_id'] ?? 0) ?: null;
    $feat = isset($_POST['is_featured']) ? 1 : 0;
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '') $errors[] = 'Name is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($sale !== null && $sale >= $price) $errors[] = 'Sale price must be lower than the regular price.';

    if (!$errors) {
        $main = $p['image'] ?? null;
        if (!empty($_FILES['image']['name'])) {
            $new = upload_image($_FILES['image'], 'products');
            if ($new) {
                delete_file($main);
                $main = $new;
            } else {
                $errors[] = 'Main image upload failed (use JPG, PNG or WEBP under 6MB).';
            }
        }
    }

    if (!$errors) {
        if ($p) {
            db_exec(
                "UPDATE products SET category_id=?, name=?, description=?, notes=?, size=?, price=?, sale_price=?, image=?, is_featured=?, is_active=? WHERE id=?",
                'issssddsiii',
                [$catId, $name, $desc, $notes, $size, $price, $sale, $main, $feat, $active, $id]
            );
            $pid = $id;
        } else {
            $pid = db_exec(
                "INSERT INTO products (category_id, name, description, notes, size, price, sale_price, image, is_featured, is_active) VALUES (?,?,?,?,?,?,?,?,?,?)",
                'issssddsii',
                [$catId, $name, $desc, $notes, $size, $price, $sale, $main, $feat, $active]
            );
        }
        // gallery removals
        foreach ((array)($_POST['remove_img'] ?? []) as $rid) {
            $g = db_row("SELECT image FROM product_images WHERE id = ? AND product_id = ?", 'ii', [(int)$rid, $pid]);
            if ($g) {
                delete_file($g['image']);
                db_exec("DELETE FROM product_images WHERE id = ?", 'i', [(int)$rid]);
            }
        }
        // gallery additions
        if (!empty($_FILES['gallery'])) {
            foreach (upload_many($_FILES['gallery'], 'products') as $path) {
                db_exec("INSERT INTO product_images (product_id, image) VALUES (?, ?)", 'is', [$pid, $path]);
            }
        }
        flash($p ? 'Product updated.' : 'Product added.');
        redirect('products.php');
    }
    // keep typed values on error
    $p = array_merge($p ?: ['image' => null], [
        'name' => $name, 'description' => $desc, 'notes' => $notes, 'size' => $size, 'price' => $price,
        'sale_price' => $saleRaw, 'category_id' => $catId, 'is_featured' => $feat, 'is_active' => $active,
    ]);
}

$gallery = $id ? db_all("SELECT * FROM product_images WHERE product_id = ? ORDER BY id", 'i', [$id]) : [];
$v = $p ?: ['name' => '', 'description' => '', 'notes' => '', 'size' => '100ml', 'price' => '', 'sale_price' => '', 'category_id' => '', 'is_featured' => 1, 'is_active' => 1, 'image' => null];

$adminTitle = $id ? 'Edit Fragrance' : 'Add Fragrance';
require __DIR__ . '/_header.php';
?>
<?php if ($errors): ?><div class="flash err"><?php foreach ($errors as $er) echo '<div>' . e($er) . '</div>'; ?></div><?php endif; ?>
<form class="panel form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="two">
    <label>Fragrance Name *<input name="name" value="<?= e($v['name']) ?>" required placeholder="e.g. Noir Oud"></label>
    <label>Category
      <select name="category_id"><option value="">— None —</option>
        <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$v['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
      </select>
    </label>
  </div>
  <div class="three">
    <label>Price (<?= e(setting('currency')) ?>) *<input name="price" type="number" step="1" min="0" value="<?= e($v['price']) ?>" required></label>
    <label>Sale Price (optional)<input name="sale_price" type="number" step="1" min="0" value="<?= e($v['sale_price']) ?>" placeholder="Leave empty if no discount"></label>
    <label>Size<input name="size" value="<?= e($v['size']) ?>" placeholder="100ml"></label>
  </div>
  <label>Fragrance Notes<input name="notes" value="<?= e($v['notes']) ?>" placeholder="Oud • Amber • Leather"></label>
  <label>Description<textarea name="description" rows="6" placeholder="Describe the scent, occasion and longevity…"><?= e($v['description']) ?></textarea></label>

  <div class="two">
    <div>
      <label>Main Image<input type="file" name="image" accept="image/*" data-preview="#mainPrev"></label>
      <div class="previews" id="mainPrev"><?php if (!empty($v['image'])): ?><img src="<?= e(img_url($v['image'])) ?>" alt=""><?php endif; ?></div>
    </div>
    <div>
      <label>More Images (select multiple)<input type="file" name="gallery[]" accept="image/*" multiple data-preview="#galPrev"></label>
      <div class="previews" id="galPrev"></div>
    </div>
  </div>

  <?php if ($gallery): ?>
    <div class="existing">
      <small class="muted">Current gallery – tick to remove</small>
      <div class="previews">
        <?php foreach ($gallery as $g): ?>
          <label class="gimg"><img src="<?= e(img_url($g['image'])) ?>" alt=""><span><input type="checkbox" name="remove_img[]" value="<?= (int)$g['id'] ?>"> Remove</span></label>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="checks">
    <label class="check"><input type="checkbox" name="is_featured" <?= $v['is_featured'] ? 'checked' : '' ?>> Show in Featured section</label>
    <label class="check"><input type="checkbox" name="is_active" <?= $v['is_active'] ? 'checked' : '' ?>> Visible on website</label>
  </div>
  <div class="form-actions">
    <button class="btn-a" type="submit"><?= $id ? 'Save Changes' : 'Add Fragrance' ?></button>
    <a class="btn-a ghost" href="products.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
