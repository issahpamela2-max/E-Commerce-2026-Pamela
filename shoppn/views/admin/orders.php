<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();   // must be before any output
require_once __DIR__ . '/../../controllers/CartController.php';

$controller = new CartController();
$all_orders = $controller->getAllOrders();
$orders     = $all_orders;
$status_options = ['paid', 'processing', 'shipped', 'delivered', 'cancelled'];
$status_filter = $_GET['status'] ?? '';
$raw_search_query = $_GET['q'] ?? '';
$search_query = is_string($raw_search_query) ? trim($raw_search_query) : '';
if (!in_array($status_filter, $status_options, true)) {
    $status_filter = '';
}
$orders = array_values(array_filter($orders, function ($order) use ($status_filter, $search_query) {
    $matches_status = $status_filter === '' || $order['order_status'] === $status_filter;
    $searchable = $order['invoice_no'] . ' ' . $order['customer_name'] . ' ' . $order['customer_email'];
    $matches_query = $search_query === '' || stripos($searchable, $search_query) !== false;
    return $matches_status && $matches_query;
}));
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>All Orders</h2>

<form class="admin-filters" method="GET">
    <label for="order-search">Search orders</label>
    <input id="order-search" type="search" name="q" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
           placeholder="Invoice, customer or email">
    <label for="order-status-filter">Status</label>
    <select id="order-status-filter" name="status">
        <option value="">All statuses</option>
        <?php foreach ($status_options as $status_option): ?>
            <option value="<?php echo $status_option; ?>" <?php echo $status_filter === $status_option ? 'selected' : ''; ?>>
                <?php echo ucfirst($status_option); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
    <a href="<?php echo BASE_URL; ?>/views/admin/orders.php">Clear</a>
</form>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <p><?php echo empty($all_orders) ? 'No orders yet.' : 'No orders match the selected filters.'; ?></p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>Invoice #</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total (GHS)</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>
                <a href="<?php echo BASE_URL; ?>/views/payment_success.php?order_id=<?php echo intval($o['order_id']); ?>">
                    <?php echo htmlspecialchars($o['invoice_no']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($o['order_date']); ?></td>
            <td><?php echo number_format(floatval($o['total']), 2); ?></td>
            <td class="status-<?php echo htmlspecialchars($o['order_status']); ?>">
                <?php echo htmlspecialchars(ucfirst($o['order_status'])); ?>
            </td>
            <td>
                <?php $next_statuses = $controller->getAvailableOrderStatuses($o['order_status']); ?>
                <?php if (!empty($next_statuses)): ?>
                <form method="POST" action="<?php echo BASE_URL; ?>/actions/update_order_status_action.php">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="order_id" value="<?php echo intval($o['order_id']); ?>">
                    <select name="order_status" class="inline-select" required>
                        <option value="" selected disabled>Select next status</option>
                        <?php foreach ($next_statuses as $s): ?>
                            <option value="<?php echo $s; ?>">
                                <?php echo ucfirst($s); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-small">Save</button>
                </form>
                <?php else: ?>
                    <span class="help-text">Final status</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
