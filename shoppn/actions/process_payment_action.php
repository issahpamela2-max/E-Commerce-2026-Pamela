<?php
require_once __DIR__ . '/../core/core.php';
require_login();
require_once __DIR__ . '/../core/paystack_cred.php';
require_once __DIR__ . '/../controllers/CartController.php';

$reference = trim($_GET['ref'] ?? '');

if (!preg_match('/^[A-Za-z0-9_-]{1,100}$/', $reference)) {
    $_SESSION['error'] = 'No payment reference supplied.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$ip = get_ip();
$customer_id = $_SESSION['customer_id'];

$controller = new CartController();

// What the cart says it should cost
$expected_total = $controller->getCartTotal($ip, $customer_id);

if ($expected_total <= 0) {
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

// ---- Ask Paystack whether this payment really happened ----
$url = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
    'Cache-Control: no-cache'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response  = curl_exec($ch);
$curl_err  = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    error_log('Paystack verify failed: ' . $curl_err);
    $_SESSION['error'] = 'Could not reach the payment provider. Please try again.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if ($http_code !== 200) {
    error_log('Paystack verify returned HTTP ' . $http_code . ' for reference ' . $reference);
    $_SESSION['error'] = 'Payment could not be verified. Please contact support if you were charged.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$result = json_decode($response, true);

// ---- Checks the response must pass ----

if (!isset($result['status']) || $result['status'] !== true) {
    $_SESSION['error'] = 'Payment could not be verified.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!isset($result['data']['status']) || $result['data']['status'] !== 'success') {
    $_SESSION['error'] = 'Payment was not successful.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!isset($result['data']['reference']) || !hash_equals($reference, (string) $result['data']['reference'])) {
    $_SESSION['error'] = 'The payment reference does not match the verified transaction.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!isset($result['data']['currency']) || $result['data']['currency'] !== 'GHS') {
    $_SESSION['error'] = 'The payment currency does not match this order.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

$verified_email = strtolower(trim($result['data']['customer']['email'] ?? ''));
$customer_email = strtolower(trim($_SESSION['customer_email'] ?? ''));
if ($verified_email === '' || $customer_email === '' || !hash_equals($customer_email, $verified_email)) {
    $_SESSION['error'] = 'The payment account does not match your customer account.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

// Paystack amounts are in pesewas. Require exact total to avoid under- or over-payment.
$expected_amount = (int) round($expected_total * 100);
$verified_amount = isset($result['data']['amount']) ? intval($result['data']['amount']) : 0;
if ($verified_amount !== $expected_amount) {
    error_log("Payment amount mismatch: paid $verified_amount, expected $expected_amount, ref $reference");
    $_SESSION['error'] = 'The amount paid does not match your cart total.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (isset($_SESSION['processed_payment_refs'][$reference])) {
    $_SESSION['error'] = 'This payment reference has already been processed.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

// ---- Verified. Record the order ----
$paid_amount = $verified_amount / 100;
$order_id = $controller->processPayment($customer_id, $paid_amount);

if ($order_id === false) {
    $_SESSION['error'] = 'Payment went through but we could not save your order. Please contact support.';
    redirect(BASE_URL . '/views/payment_failed.php');
}

if (!isset($_SESSION['processed_payment_refs'])) {
    $_SESSION['processed_payment_refs'] = [];
}
$_SESSION['processed_payment_refs'][$reference] = true;
if (count($_SESSION['processed_payment_refs']) > 20) {
    $_SESSION['processed_payment_refs'] = array_slice(
        $_SESSION['processed_payment_refs'],
        -20,
        null,
        true
    );
}

$_SESSION['success'] = 'Payment successful. Thank you for your order.';
redirect(BASE_URL . '/views/payment_success.php?order_id=' . $order_id);