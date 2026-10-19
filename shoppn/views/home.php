<?php
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$cat_id   = intval($_GET['cat'] ?? 0);
$brand_id = intval($_GET['brand'] ?? 0);

if ($cat_id > 0) {
    $products = $controller->getProductsByCategory($cat_id);
    $cat      = $controller->getCategoryById($cat_id);
    $heading  = $cat ? htmlspecialchars($cat['cat_name']) : 'Category';
} elseif ($brand_id > 0) {
    $products = $controller->getProductsByBrand($brand_id);
    $brand    = $controller->getBrandById($brand_id);
    $heading  = $brand ? htmlspecialchars($brand['brand_name']) : 'Brand';
} else {
    $products = $controller->getFeaturedProducts(6);
    $heading  = 'Featured Products';
}
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <div class="main-content">

        <h2><?php echo $heading; ?></h2>

        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if ($p['product_image']): ?>
                        <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>"
                             alt="<?php echo htmlspecialchars($p['product_title']); ?>">
                    <?php endif; ?>

                    <h4><?php echo htmlspecialchars($p['product_title']); ?></h4>
                    <p class="price">GHS <?php echo number_format($p['product_price'], 2); ?></p>

                    <div class="card-actions">
                        <a href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo $p['product_id']; ?>">Details</a>
                        <a href="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php?add_cart=<?php echo $p['product_id']; ?>">Add to Cart</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>