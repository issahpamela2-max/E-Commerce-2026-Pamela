<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$id = intval($_POST['del_id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$controller = new ProductController();
$result     = $controller->deleteBrand($id);

if ($result === 'in_use') {
    $_SESSION['error'] = 'Cannot delete this brand — one or more products still belong to it. '
                       . 'Reassign or delete those products first.';
} elseif ($result) {
    $_SESSION['success'] = 'Brand deleted.';
} else {
    $_SESSION['error'] = 'Could not delete brand. Please try again.';
}

redirect(BASE_URL . '/views/admin/brand.php');
