<?php
require_once __DIR__ . '/../core/core.php';
require_admin();   // must be before any output or DB work

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/admin/orders.php');
}

if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Your request could not be verified. Please try again.';
    redirect(BASE_URL . '/views/admin/orders.php');
}

require_once __DIR__ . '/../controllers/CartController.php';

$order_id = intval($_POST['order_id'] ?? 0);
$status   = trim($_POST['order_status'] ?? '');

if ($order_id <= 0 || $status === '') {
    $_SESSION['error'] = 'Invalid request.';
    redirect(BASE_URL . '/views/admin/orders.php');
}

$controller = new CartController();

if ($controller->updateOrderStatus($order_id, $status)) {
    $_SESSION['success'] = 'Order #' . $order_id . ' status updated to ' . ucfirst($status) . '.';
} else {
    $_SESSION['error'] = 'Could not update status. The requested order-status transition is not available.';
}

redirect(BASE_URL . '/views/admin/orders.php');
