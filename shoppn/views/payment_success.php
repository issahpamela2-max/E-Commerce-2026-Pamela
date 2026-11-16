<?php
require_once __DIR__ . '/../core/core.php';
require_login();
require_once __DIR__ . '/../controllers/CartController.php';

$order_id = intval($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    redirect(BASE_URL . '/index.php');
}

$controller = new CartController();
$order = $controller->getOrderById($order_id);

// Customers can only view their own invoices; admins can view invoices from
// the order-management screen.
if (!$order || (!is_admin() && intval($order['customer_id']) !== intval($_SESSION['customer_id']))) {
    $_SESSION['error'] = 'Order not found.';
    redirect(BASE_URL . '/index.php');
}

$items = $controller->getOrderItems($order_id);
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="invoice">

    <h2>Invoice</h2>

    <div class="invoice-meta">
        <p class="meta-line"><strong>Invoice Number:</strong> <?php echo htmlspecialchars($order['invoice_no']); ?></p>
        <p class="meta-line"><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p class="meta-line"><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p class="meta-line"><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p class="meta-line"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
    </div>

    <h3>Order Progress</h3>
    <?php include __DIR__ . '/layout/order_progress.php'; ?>

    <table class="data-table">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
        </tr>

        <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['product_title']); ?></td>
            <td><?php echo intval($item['qty']); ?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="total-row">
            <td><strong>Total Paid</strong></td>
            <td><strong>GHS <?php echo number_format($order['amt'] ?? 0, 2); ?></strong></td>
        </tr>
    </table>

    <div class="cart-actions">
        <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
        <button type="button" onclick="window.print();">Print Invoice</button>
    </div>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>