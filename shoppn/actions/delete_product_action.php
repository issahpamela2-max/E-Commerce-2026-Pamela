<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/admin/product.php');
}

$id = intval($_POST['del_id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid product.';
    redirect(BASE_URL . '/views/admin/product.php');
}

$controller = new ProductController();

if ($controller->deleteProduct($id)) {
    $_SESSION['success'] = 'Product deleted.';
} else {
    $_SESSION['error'] = 'Could not delete product. Please try again.';
}

redirect(BASE_URL . '/views/admin/product.php');
