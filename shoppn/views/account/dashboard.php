<?php
require_once __DIR__ . '/../../core/core.php';
require_login();
require_once __DIR__ . '/../../controllers/CustomerController.php';
require_once __DIR__ . '/../../controllers/CartController.php';

$customerController = new CustomerController();
$customer = $customerController->getCustomerById($_SESSION['customer_id']);

if (!$customer) {
    $_SESSION['error'] = 'Could not load your account.';
    redirect(BASE_URL . '/index.php');
}

$cartController = new CartController();
$orders = $cartController->getOrdersByCustomer($_SESSION['customer_id']);
$recentOrders = array_slice($orders, 0, 5);
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>My Dashboard</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<section class="account-summary">
    <h3>Welcome, <?php echo htmlspecialchars($customer['customer_name']); ?></h3>
    <p><?php echo htmlspecialchars($customer['customer_email']); ?></p>
    <p>You have placed <?php echo count($orders); ?> order<?php echo count($orders) === 1 ? '' : 's'; ?>.</p>
</section>

<h3>Recent Orders</h3>

<?php if (empty($recentOrders)): ?>
    <p>You have not placed any orders yet. <a href="<?php echo BASE_URL; ?>/index.php">Start shopping</a>.</p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>Invoice #</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total (GHS)</th>
            <th>Details</th>
        </tr>
        <?php foreach ($recentOrders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['invoice_no']); ?></td>
            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            <td class="status-<?php echo htmlspecialchars($order['order_status']); ?>">
                <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?>
                <?php include __DIR__ . '/../layout/order_progress.php'; ?>
            </td>
            <td><?php echo number_format(floatval($order['total']), 2); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/views/payment_success.php?order_id=<?php echo intval($order['order_id']); ?>">
                    View Invoice
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
