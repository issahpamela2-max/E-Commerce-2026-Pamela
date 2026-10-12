<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$query = trim(strip_tags($_GET['user_query'] ?? ''));

$controller = new ProductController();
$products = ($query === '') ? [] : $controller->searchProducts($query);
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <div class="main-content">

        <h2>Search Results</h2>

        <?php if ($query === ''): ?>
            <p>Please enter something to search for.</p>

        <?php elseif (empty($products)): ?>
            <p>No products found for "<?php echo htmlspecialchars($query); ?>".</p>

        <?php else: ?>
            <p class="meta-line">
                <?php echo count($products); ?>
                result<?php echo count($products) === 1 ? '' : 's'; ?>
                for "<?php echo htmlspecialchars($query); ?>"
            </p>

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
                        <a href="<?php echo BASE_URL; ?>/index.php?add_cart=<?php echo $p['product_id']; ?>">Add to Cart</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a class="back-link" href="<?php echo BASE_URL; ?>/index.php">&larr; Back to products</a>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>