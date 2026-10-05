<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/category.php');
}

$id   = intval($_POST['cat_id'] ?? 0);
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid category selected.';
    redirect(BASE_URL . '/views/admin/category.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Category name is required and must be 100 characters or fewer.';
    redirect(BASE_URL . '/views/admin/category.php');
}

$controller = new ProductController();

if ($controller->updateCategory($id, $name)) {
    $_SESSION['success'] = 'Category updated.';
} else {
    $_SESSION['error'] = 'Could not update category. Please try again.';
}

redirect(BASE_URL . '/views/admin/category.php');