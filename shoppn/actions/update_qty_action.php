<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$ids  = $_POST['update'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (!is_array($ids) || !is_array($qtys) || count($ids) !== count($qtys)) {
    $_SESSION['error'] = 'Could not update quantities.';
    redirect(BASE_URL . '/views/cart.php');
}

$ip = get_ip();
$controller = new CartController();

$updated = 0;
$invalid = 0;

for ($i = 0; $i < count($ids); $i++) {
    $product_id = intval($ids[$i]);
    $qty        = intval($qtys[$i]);

    if ($product_id <= 0) {
        continue;
    }

    if ($qty < 1) {
        $invalid++;
        continue;
    }

    if ($controller->updateQty($product_id, $qty, $ip)) {
        $updated++;
    }
}

if ($invalid > 0) {
    $_SESSION['error'] = 'Quantities must be at least 1. Use Remove to take an item out of your cart.';
} elseif ($updated > 0) {
    $_SESSION['success'] = 'Cart updated.';
}

redirect(BASE_URL . '/views/cart.php');