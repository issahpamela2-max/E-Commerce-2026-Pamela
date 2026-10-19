<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/cart.php');
}

$remove = $_POST['remove'] ?? [];

if (!is_array($remove) || empty($remove)) {
    $_SESSION['error'] = 'Please tick the items you want to remove.';
    redirect(BASE_URL . '/views/cart.php');
}

$ip = get_ip();
$controller = new CartController();
$removed = 0;

foreach ($remove as $value) {
    $product_id = intval($value);

    if ($product_id > 0 && $controller->removeFromCart($product_id, $ip)) {
        $removed++;
    }
}

if ($removed > 0) {
    $_SESSION['success'] = $removed . ' item' . ($removed === 1 ? '' : 's') . ' removed from your cart.';
} else {
    $_SESSION['error'] = 'Nothing was removed.';
}

redirect(BASE_URL . '/views/cart.php');