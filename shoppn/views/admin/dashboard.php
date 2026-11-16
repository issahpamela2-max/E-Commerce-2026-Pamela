<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';
require_once __DIR__ . '/../../controllers/CartController.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';

$pc = new ProductController();
$cc = new CartController();
$cu = new CustomerController();

$brands_all     = $pc->getAllBrands();
$categories_all = $pc->getAllCategories();
$products_all   = $pc->getAllProducts();
$orders_all     = $cc->getAllOrders();

$counts = [
    'Brands'     => count($brands_all),
    'Categories' => count($categories_all),
    'Products'   => count($products_all),
    'Orders'     => count($orders_all),
    'Customers'  => $cu->countCustomers(),
];
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>Admin Dashboard</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="dashboard-grid">

    <a class="dash-card" href="<?php echo BASE_URL; ?>/views/admin/brand.php">
        <span class="dash-count"><?php echo $counts['Brands']; ?></span>
        <span class="dash-label">Brands</span>
    </a>

    <a class="dash-card" href="<?php echo BASE_URL; ?>/views/admin/category.php">
        <span class="dash-count"><?php echo $counts['Categories']; ?></span>
        <span class="dash-label">Categories</span>
    </a>

    <a class="dash-card" href="<?php echo BASE_URL; ?>/views/admin/product.php">
        <span class="dash-count"><?php echo $counts['Products']; ?></span>
        <span class="dash-label">Products</span>
    </a>

    <a class="dash-card" href="<?php echo BASE_URL; ?>/views/admin/orders.php">
        <span class="dash-count"><?php echo $counts['Orders']; ?></span>
        <span class="dash-label">Orders</span>
    </a>

    <a class="dash-card dash-card--customers" href="<?php echo BASE_URL; ?>/views/admin/customers.php">
        <span class="dash-count"><?php echo $counts['Customers']; ?></span>
        <span class="dash-label">Customers</span>
    </a>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
