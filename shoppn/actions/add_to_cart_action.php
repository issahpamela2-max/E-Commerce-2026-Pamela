<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$product_id = intval($_GET['add_cart'] ?? 0);

// Where to send the shopper back to
$back = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php');

if ($product_id <= 0) {
    $_SESSION['error'] = 'Invalid product.';
    redirect($back);
}

$ip = get_ip();
$customer_id = is_logged_in() ? $_SESSION['customer_id'] : null;

$controller = new CartController();

if ($controller->isInCart($product_id, $ip)) {
    $_SESSION['error'] = 'This item is already in your cart. Go to cart to update quantity.';
    redirect($back);
}

if ($controller->addToCart($product_id, 1, $ip, $customer_id)) {
    $_SESSION['success'] = 'Item added to your cart.';
} else {
    $_SESSION['error'] = 'Could not add item to cart. Please try again.';
}

redirect($back);