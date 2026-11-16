<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/admin/category.php');
}

$id = intval($_POST['del_id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid category.';
    redirect(BASE_URL . '/views/admin/category.php');
}

$controller = new ProductController();
$result     = $controller->deleteCategory($id);

if ($result === 'in_use') {
    $_SESSION['error'] = 'Cannot delete this category — one or more products still belong to it. '
                       . 'Reassign or delete those products first.';
} elseif ($result) {
    $_SESSION['success'] = 'Category deleted.';
} else {
    $_SESSION['error'] = 'Could not delete category. Please try again.';
}

redirect(BASE_URL . '/views/admin/category.php');
