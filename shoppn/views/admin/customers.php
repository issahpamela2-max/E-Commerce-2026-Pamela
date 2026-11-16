<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/CustomerController.php';

$controller = new CustomerController();
$all_customers = $controller->getAllCustomers();
$customers = $all_customers;
$raw_search_query = $_GET['q'] ?? '';
$search_query = is_string($raw_search_query) ? trim($raw_search_query) : '';
$customers = array_values(array_filter($customers, function ($customer) use ($search_query) {
    if ($search_query === '') {
        return true;
    }
    $searchable = implode(' ', [
        $customer['customer_name'],
        $customer['customer_email'],
        $customer['customer_country'],
        $customer['customer_city'],
        $customer['customer_contact']
    ]);
    return stripos($searchable, $search_query) !== false;
}));
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>Customers</h2>

<p><a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php">Back to Admin Dashboard</a></p>

<form class="admin-filters" method="GET">
    <label for="customer-search">Search customers</label>
    <input id="customer-search" type="search" name="q" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
           placeholder="Name, email, city or contact">
    <button type="submit">Search</button>
    <a href="<?php echo BASE_URL; ?>/views/admin/customers.php">Clear</a>
</form>

<?php if (empty($customers)): ?>
    <p><?php echo empty($all_customers) ? 'No customers have registered yet.' : 'No customers match your search.'; ?></p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Country</th>
            <th>City</th>
            <th>Contact</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
            <td><?php echo htmlspecialchars($customer['customer_country']); ?></td>
            <td><?php echo htmlspecialchars($customer['customer_city']); ?></td>
            <td><?php echo htmlspecialchars($customer['customer_contact']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
