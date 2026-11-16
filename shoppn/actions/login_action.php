<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/login.php');
}

if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/login.php');
}

$email = trim(strip_tags($_POST['email'] ?? ''));
$pass  = $_POST['pass'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    $_SESSION['error'] = 'Please enter a valid email and password.';
    redirect(BASE_URL . '/views/login.php');
}

$controller = new CustomerController();
$result = $controller->login($email, $pass);

if ($result['success']) {
    $c = $result['customer'];

    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['customer_id']    = $c['customer_id'];
    $_SESSION['customer_name']  = $c['customer_name'];
    $_SESSION['customer_email'] = $c['customer_email'];
    $_SESSION['user_role']      = $c['user_role'];

    if (!(new CartController())->mergeGuestCart(get_ip(), intval($c['customer_id']))) {
        $_SESSION['error'] = 'Signed in, but your guest cart could not be restored.';
    }

    $redirect_after_login = $_SESSION['redirect_after_login'] ?? null;
    unset($_SESSION['redirect_after_login']);
    if (!is_admin() && $redirect_after_login === BASE_URL . '/views/payment.php') {
        redirect(BASE_URL . '/views/payment.php');
    }

    redirect(is_admin()
        ? BASE_URL . '/views/admin/dashboard.php'
        : BASE_URL . '/views/account/dashboard.php');
} else {
    $_SESSION['error'] = $result['error'];
    redirect(BASE_URL . '/views/login.php');
}