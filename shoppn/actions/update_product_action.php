<?php
require_once __DIR__ . '/../core/core.php';
require_admin();
require_once __DIR__ . '/../controllers/ProductController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/product.php');
}

$id       = intval($_POST['product_id'] ?? 0);
$cat      = intval($_POST['product_cat'] ?? 0);
$brand    = intval($_POST['product_brand'] ?? 0);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = floatval($_POST['product_price'] ?? 0);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));
$existing = trim($_POST['existing_image'] ?? '');

$errors = [];

if ($id <= 0)    { $errors[] = 'Invalid product selected.'; }
if ($cat <= 0)   { $errors[] = 'Please select a category.'; }
if ($brand <= 0) { $errors[] = 'Please select a brand.'; }

if ($title === '' || strlen($title) > 200) {
    $errors[] = 'Product title is required and must be 200 characters or fewer.';
}

if ($price <= 0) {
    $errors[] = 'Price must be greater than zero.';
}

if (strlen($desc) > 500) {
    $errors[] = 'Description must be 500 characters or fewer.';
}

if (strlen($keywords) > 100) {
    $errors[] = 'Keywords must be 100 characters or fewer.';
}

// ---- Image: only replace if a new one was chosen ----
$filename    = $existing;
$new_upload  = false;

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {

    $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024;

    $info = getimagesize($_FILES['product_image']['tmp_name']);

    if ($info === false || !in_array($info['mime'], $allowed)) {
        $errors[] = 'Image must be a JPG, PNG, GIF or WEBP file.';
    } elseif ($_FILES['product_image']['size'] > $max_size) {
        $errors[] = 'Image must be 2MB or smaller.';
    } else {
        $filename   = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $new_upload = true;
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
}

if ($new_upload) {
    $target = __DIR__ . '/../images/products/' . $filename;

    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target)) {
        $_SESSION['error'] = 'Could not save the new image.';
        redirect(BASE_URL . '/views/admin/product.php?edit_id=' . $id);
    }
}

$controller = new ProductController();

if ($controller->updateProduct($id, $cat, $brand, $title, $price, $desc, $filename, $keywords)) {
    $_SESSION['success'] = 'Product updated.';
} else {
    $_SESSION['error'] = 'Could not update product. Please try again.';
}

redirect(BASE_URL . '/views/admin/product.php');