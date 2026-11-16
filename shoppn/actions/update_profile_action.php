<?php
require_once __DIR__ . '/../core/core.php';
require_login();
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/account/my_account.php');
}

if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please refresh the page and try again.';
    redirect(BASE_URL . '/views/account/my_account.php');
}

$id = $_SESSION['customer_id'];

$name    = trim(strip_tags($_POST['name'] ?? ''));
$country = trim(strip_tags($_POST['country'] ?? ''));
$city    = trim(strip_tags($_POST['city'] ?? ''));
$contact = trim(strip_tags($_POST['contact'] ?? ''));

$contact = preg_replace('/[\s\-]/', '', $contact);

$errors = [];

if (strlen($name) < 2 || strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters.';
}

if ($country === '' || strlen($country) > 30) {
    $errors[] = 'Country is required and must be 30 characters or fewer.';
}

if (strlen($city) < 2 || strlen($city) > 30) {
    $errors[] = 'City must be between 2 and 30 characters.';
}

if (!preg_match('/^\+?[0-9]{9,14}$/', $contact)) {
    $errors[] = 'Enter a valid phone number, 9 to 14 digits, optionally starting with +.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect(BASE_URL . '/views/account/my_account.php');
}

$controller = new CustomerController();

if ($controller->updateProfile($id, $name, $country, $city, $contact)) {
    // Keep the header in step with the new name
    $_SESSION['customer_name'] = $name;
    $_SESSION['success'] = 'Your details have been updated.';
} else {
    $_SESSION['error'] = 'Could not update your details. Please try again.';
}

redirect(BASE_URL . '/views/account/my_account.php');