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

$current_pass = $_POST['current_pass'] ?? '';
$new_pass     = $_POST['new_pass'] ?? '';
$confirm_pass = $_POST['confirm_pass'] ?? '';

$errors = [];

if ($current_pass === '') {
    $errors[] = 'Please enter your current password.';
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $new_pass)) {
    $errors[] = 'New password must be at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';
}

if ($new_pass !== $confirm_pass) {
    $errors[] = 'New password and confirmation do not match.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect(BASE_URL . '/views/account/my_account.php');
}

$controller = new CustomerController();
$result = $controller->changePassword($id, $current_pass, $new_pass);

if ($result['success']) {
    $_SESSION['success'] = 'Your password has been changed.';
} else {
    $_SESSION['error'] = $result['error'];
}

redirect(BASE_URL . '/views/account/my_account.php');