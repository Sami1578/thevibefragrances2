<?php
require_once __DIR__ . '/includes/functions.php';

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('cart.php'));
}

$token = $_POST['csrf'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(419);
        echo json_encode(['ok' => false, 'msg' => 'Session expired, please refresh.']);
        exit;
    }
    redirect(url('cart.php'));
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['id'] ?? 0);
$qty = max(1, min(20, (int)($_POST['qty'] ?? 1)));
$msg = '';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        $exists = db_row("SELECT id, name FROM products WHERE id = ? AND is_active = 1", 'i', [$id]);
        if ($exists) {
            $_SESSION['cart'][$id] = min(20, ($_SESSION['cart'][$id] ?? 0) + $qty);
            $msg = $exists['name'] . ' added to cart';
        }
        break;
    case 'update':
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $qty;
        }
        break;
    case 'remove':
        unset($_SESSION['cart'][$id]);
        break;
    case 'clear':
        $_SESSION['cart'] = [];
        break;
}

if ($isAjax) {
    $t = cart_totals();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'count' => cart_count(), 'total' => money($t['subtotal']), 'msg' => $msg]);
    exit;
}
redirect(url('cart.php'));
