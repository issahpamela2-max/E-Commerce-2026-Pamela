<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/register.php');
}

// Clean the inputs
$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim(strip_tags($_POST['email'] ?? ''));
$pass    = $_POST['pass'] ?? '';
$country = trim(strip_tags($_POST['country'] ?? ''));
$city    = trim(strip_tags($_POST['city'] ?? ''));
$contact = trim(strip_tags($_POST['contact'] ?? ''));

// Strip spaces and hyphens from the phone number before validating
$contact = preg_replace('/[\s\-]/', '', $contact);

$errors = [];

if (strlen($name) < 2 || strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
} elseif (strlen($email) > 50) {
    $errors[] = 'Email must be 50 characters or fewer.';
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $pass)) {
    $errors[] = 'Password must be at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';
}

if ($country === '') { $errors[] = 'Please select a country.'; }
if ($city === '')    { $errors[] = 'Please enter your city.'; }

if (!preg_match('/^\+?[0-9]{9,14}$/', $contact)) {
    $errors[] = 'Enter a valid phone number, 9 to 14 digits, optionally starting with +.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect(BASE_URL . '/views/register.php');
}

// Hand off to the controller
$controller = new CustomerController();
$result = $controller->register([
    'name'    => $name,
    'email'   => $email,
    'pass'    => $pass,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact
]);

if ($result['success']) {
    $_SESSION['customer_id']    = $result['customer_id'];
    $_SESSION['customer_name']  = $name;
    $_SESSION['customer_email'] = $email;
    $_SESSION['user_role']      = 2;
    redirect(BASE_URL . '/index.php');
} else {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/register.php');
}