<?php
require_once __DIR__ . '/_auth.php';

$id = (int)($_GET['id'] ?? 0);
$b = $id ? db_row("SELECT * FROM banners WHERE id = ?", 'i', [$id]) : null;
if ($id && !$b) {
    redirect('banners.php');
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $title = trim($_POST['title'] ?? '');
    $sub = trim($_POST['subtitle'] ?? '');
    $btn = trim($_POST['button_text'] ?? '');
    $link = trim($_POST['button_link'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    if ($title === '') $errors[] = 'Title is required.';

    $img = $b['image'] ?? null;
    if (!$errors && !empty($_FILES['image']['name'])) {
        $new = upload_image($_FILES['image'], 'banners');
        if ($new) {
            delete_file($img);
            $img = $new;
        } else {
            $errors[] = 'Image upload failed (JPG, PNG or WEBP under 6MB).';
        }
    }
    if (!$errors) {
        if ($b) {
            db_exec("UPDATE banners SET title=?, subtitle=?, button_text=?, button_link=?, image=?, sort_order=?, is_active=? WHERE id=?",
                'sssssiii', [$title, $sub, $btn, $link, $img, $order, $active, $id]);
        } else {
            db_exec("INSERT INTO banners (title, subtitle, button_text, button_link, image, sort_order, is_active) VALUES (?,?,?,?,?,?,?)",
                'sssssii', [$title, $sub, $btn, $link, $img, $order, $active]);
        }
        flash($b ? 'Banner updated.' : 'Banner added.');
        redirect('banners.php');
    }
    $b = array_merge($b ?: ['image' => null], ['title' => $title, 'subtitle' => $sub, 'button_text' => $btn, 'button_link' => $link, 'sort_order' => $order, 'is_active' => $active]);
}
$v = $b ?: ['title' => '', 'subtitle' => '', 'button_text' => 'Shop Now', 'button_link' => 'shop.php', 'sort_order' => 0, 'is_active' => 1, 'image' => null];

$adminTitle = $id ? 'Edit Banner' : 'Add Banner';
require __DIR__ . '/_header.php';
?>
<?php if ($errors): ?><div class="flash err"><?php foreach ($errors as $er) echo '<div>' . e($er) . '</div>'; ?></div><?php endif; ?>
<form class="panel form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <label>Headline *<input name="title" value="<?= e($v['title']) ?>" required placeholder="Discover Your Signature Scent"></label>
  <label>Sub-text<input name="subtitle" value="<?= e($v['subtitle']) ?>" placeholder="A short line under the headline"></label>
  <div class="three">
    <label>Button Text<input name="button_text" value="<?= e($v['button_text']) ?>"></label>
    <label>Button Link<input name="button_link" value="<?= e($v['button_link']) ?>" placeholder="shop.php or https://…"></label>
    <label>Order (small = first)<input name="sort_order" type="number" value="<?= (int)$v['sort_order'] ?>"></label>
  </div>
  <label>Banner Image (optional – wide image, ~1920×800)<input type="file" name="image" accept="image/*" data-preview="#bPrev"></label>
  <div class="previews wide" id="bPrev"><?php if (!empty($v['image'])): ?><img src="<?= e(img_url($v['image'])) ?>" alt=""><?php endif; ?></div>
  <p class="muted">Without an image, a luxury gradient background is used.</p>
  <div class="checks"><label class="check"><input type="checkbox" name="is_active" <?= $v['is_active'] ? 'checked' : '' ?>> Show on website</label></div>
  <div class="form-actions">
    <button class="btn-a" type="submit"><?= $id ? 'Save Changes' : 'Add Banner' ?></button>
    <a class="btn-a ghost" href="banners.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
