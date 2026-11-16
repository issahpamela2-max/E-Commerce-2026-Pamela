<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/brand.php');
}

if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($name === '' || strlen($name) > 100) {
    $_SESSION['error'] = 'Brand name is required and must be 100 characters or fewer.';
    redirect(BASE_URL . '/views/admin/brand.php');
}

$controller = new ProductController();

if ($controller->addBrand($name)) {
    $_SESSION['success'] = 'Brand added.';
} else {
    $_SESSION['error'] = 'Could not add brand. Please try again.';
}

redirect(BASE_URL . '/views/admin/brand.php');