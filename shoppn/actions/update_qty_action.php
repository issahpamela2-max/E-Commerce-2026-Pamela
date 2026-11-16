<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please refresh your cart and try again.';
    redirect(BASE_URL . '/views/cart.php');
}

$ids  = $_POST['update'] ?? [];
$qtys = $_POST['qty'] ?? [];

if (!is_array($ids) || !is_array($qtys) || count($ids) !== count($qtys)) {
    $_SESSION['error'] = 'Could not update quantities.';
    redirect(BASE_URL . '/views/cart.php');
}

$ip = get_ip();
$customer_id = get_cart_customer_id();
$controller = new CartController();

$updated = 0;
$invalid = 0;
$unavailable = 0;

for ($i = 0; $i < count($ids); $i++) {
    $product_id = intval($ids[$i]);

    if ($product_id <= 0) {
        continue;
    }

    if (!is_string($qtys[$i]) || !preg_match('/^[1-9]\d*$/', $qtys[$i])) {
        $invalid++;
        continue;
    }

    $qty = intval($qtys[$i]);
    if ($controller->updateQty($product_id, $qty, $ip, $customer_id)) {
        $updated++;
    } else {
        $unavailable++;
    }
}

if ($invalid > 0) {
    $_SESSION['error'] = 'Quantities must be at least 1. Use Remove to take an item out of your cart.';
} elseif ($unavailable > 0) {
    $_SESSION['error'] = 'One or more cart items could not be updated. Please refresh your cart and try again.';
} elseif ($updated > 0) {
    $_SESSION['success'] = 'Cart updated.';
} else {
    $_SESSION['error'] = 'No cart quantities were changed.';
}

redirect(BASE_URL . '/views/cart.php');