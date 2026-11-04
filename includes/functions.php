<?php
require_once __DIR__ . '/db.php';

function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function setting($key, $default = '')
{
    global $db;
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $r = $db->query("SELECT k, v FROM settings");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $cache[$row['k']] = $row['v'];
            }
        }
    }
    return $cache[$key] ?? $default;
}

function money($n)
{
    return setting('currency', 'Rs.') . ' ' . number_format((float)$n, 0);
}

function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function img_url($path)
{
    return $path ? url($path) : '';
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

/* ---------- CSRF ---------- */
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check()
{
    $t = $_POST['csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419);
        die('Session expired. Please go back, refresh the page and try again.');
    }
}

/* ---------- Flash messages ---------- */
function flash($msg = null, $type = 'ok')
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['m' => $msg, 't' => $type];
        return;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Images ---------- */
function upload_image($file, $dir = 'products')
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > 6 * 1024 * 1024) {
        return null;
    }
    $info = @getimagesize($file['tmp_name']);
    if (!$info) {
        return null;
    }
    $map = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!isset($map[$info[2]])) {
        return null;
    }
    $folder = ROOT_PATH . '/uploads/' . $dir;
    if (!is_dir($folder)) {
        @mkdir($folder, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '.' . $map[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $folder . '/' . $name)) {
        return null;
    }
    return 'uploads/' . $dir . '/' . $name;
}

function upload_many($files, $dir = 'products')
{
    $out = [];
    if (!isset($files['name']) || !is_array($files['name'])) {
        return $out;
    }
    foreach ($files['name'] as $i => $n) {
        $one = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
        $p = upload_image($one, $dir);
        if ($p) {
            $out[] = $p;
        }
    }
    return $out;
}

function delete_file($path)
{
    if ($path && strpos($path, 'uploads/') === 0) {
        $full = ROOT_PATH . '/' . $path;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

function placeholder_svg()
{
    return '<svg class="ph-bottle" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <rect x="82" y="14" width="36" height="30" rx="4" fill="#c9a24b"/>
      <rect x="90" y="44" width="20" height="16" fill="#e8d8a6" opacity=".7"/>
      <rect x="40" y="60" width="120" height="170" rx="22" fill="#ffffff" fill-opacity=".08" stroke="#c9a24b" stroke-opacity=".7" stroke-width="2"/>
      <rect x="52" y="120" width="96" height="92" rx="12" fill="#c9a24b" fill-opacity=".18"/>
      <text x="100" y="168" text-anchor="middle" font-family="Georgia,serif" font-size="14" fill="#c9a24b" letter-spacing="3">THE VIBE</text>
      <path d="M58 76 q6 40 0 120" stroke="#fff" stroke-opacity=".25" stroke-width="3" fill="none" stroke-linecap="round"/>
    </svg>';
}

function product_img($p, $class = '')
{
    if (!empty($p['image'])) {
        return '<img class="' . e($class) . '" src="' . e(img_url($p['image'])) . '" alt="' . e($p['name']) . '" loading="lazy">';
    }
    return '<div class="ph-wrap ' . e($class) . '">' . placeholder_svg() . '</div>';
}

/* ---------- Pricing ---------- */
function effective_price($p)
{
    $sp = (float)($p['sale_price'] ?? 0);
    $pr = (float)$p['price'];
    return ($sp > 0 && $sp < $pr) ? $sp : $pr;
}

function on_sale($p)
{
    $sp = (float)($p['sale_price'] ?? 0);
    return $sp > 0 && $sp < (float)$p['price'];
}

/* ---------- Cart ---------- */
function cart_get()
{
    return $_SESSION['cart'] ?? [];
}

function cart_items()
{
    $cart = cart_get();
    if (!$cart) {
        return [];
    }
    $ids = array_map('intval', array_keys($cart));
    $in = implode(',', $ids);
    $rows = db_all("SELECT * FROM products WHERE is_active = 1 AND id IN ($in)");
    $items = [];
    foreach ($rows as $r) {
        $qty = max(1, (int)$cart[$r['id']]);
        $r['qty'] = $qty;
        $r['unit'] = effective_price($r);
        $r['line'] = $r['unit'] * $qty;
        $items[] = $r;
    }
    return $items;
}

function cart_count()
{
    return array_sum(array_map('intval', cart_get()));
}

function cart_totals($items = null)
{
    $items = $items ?? cart_items();
    $sub = 0;
    foreach ($items as $i) {
        $sub += $i['line'];
    }
    $fee = 0;
    if ($sub > 0) {
        $free = (float)setting('free_delivery_over', 0);
        $fee = ($free > 0 && $sub >= $free) ? 0 : (float)setting('delivery_fee', 0);
    }
    return ['subtotal' => $sub, 'delivery' => $fee, 'total' => $sub + $fee];
}

function wa_link($text = '')
{
    $num = preg_replace('/\D+/', '', setting('whatsapp', ''));
    return 'https://wa.me/' . $num . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}
