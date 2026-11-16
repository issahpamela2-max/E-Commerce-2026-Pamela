<?php
require_once __DIR__ . '/../../core/core.php';
require_login();   // redirects to login if not signed in
require_once __DIR__ . '/../../controllers/CartController.php';

$controller = new CartController();

// getOrdersByCustomer only returns rows WHERE customer_id = this session user,
// so a customer can never see another customer's orders even if they change the URL.
$orders = $controller->getOrdersByCustomer($_SESSION['customer_id']);
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>My Orders</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($orders)): ?>
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
        <?php foreach ($orders as $order): ?>
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
