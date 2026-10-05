<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

$id   = intval($_POST['brand_id'] ?? 0);
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid brand selected.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required and must be 100 characters or fewer.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$controller = new ProductController();

if ($controller->updateBrand($id, $name)) {
    $_SESSION['success'] = 'Brand updated.';
} else {
    $_SESSION['error'] = 'Could not update brand. Please try again.';
}

redirect(BASE_URL . '/views/admin/brand.php');