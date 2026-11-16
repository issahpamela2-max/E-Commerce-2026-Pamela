<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$product_id = intval($_POST['add_cart'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please refresh the page and try again.';
    redirect(BASE_URL . '/index.php');
}

if ($product_id <= 0) {
    $_SESSION['error'] = 'Invalid product.';
    redirect(BASE_URL . '/index.php');
}

$ip = get_ip();

$product = (new ProductController())->getProductById($product_id);
$back = BASE_URL . '/views/single_product.php?pro_id=' . $product_id;

if (!$product) {
    $_SESSION['error'] = 'This product is no longer available.';
    redirect(BASE_URL . '/index.php');
}

$controller = new CartController();
$customer_id = get_cart_customer_id();

if ($controller->isInCart($product_id, $ip, $customer_id)) {
    $_SESSION['error'] = 'This item is already in your cart. Go to cart to update quantity.';
    redirect($back);
}

if ($controller->addToCart($product_id, 1, $ip, $customer_id)) {
    $_SESSION['success'] = 'Item added to your cart.';
    redirect(BASE_URL . '/views/cart.php');
} else {
    $_SESSION['error'] = 'Could not add this product. Please try again.';
}

redirect($back);