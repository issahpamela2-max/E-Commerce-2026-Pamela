<?php
require_once __DIR__ . '/../core/core.php';
require_login();
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

if ($country === '') { $errors[] = 'Please select a country.'; }
if (strlen($city) < 2) { $errors[] = 'Please enter your city.'; }

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