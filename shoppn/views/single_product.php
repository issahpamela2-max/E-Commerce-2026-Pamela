<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$pro_id = intval($_GET['pro_id'] ?? 0);

if ($pro_id <= 0) {
    redirect(BASE_URL . '/index.php');
}

$controller = new ProductController();
$product = $controller->getProductById($pro_id);

if (!$product) {
    redirect(BASE_URL . '/index.php');
}
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <div class="main-content">

        <div class="product-detail">

            <?php if ($product['product_image']): ?>
                <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($product['product_image']); ?>"
                     alt="<?php echo htmlspecialchars($product['product_title']); ?>">
            <?php endif; ?>

            <div class="product-detail-info">
                <h2><?php echo htmlspecialchars($product['product_title']); ?></h2>

                <p class="price">GHS <?php echo number_format($product['product_price'], 2); ?></p>

                <p class="meta-line">
                    <strong>Category:</strong>
                    <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo $product['product_cat']; ?>">
                        <?php echo htmlspecialchars($product['cat_name']); ?>
                    </a>
                </p>

                <p class="meta-line">
                    <strong>Brand:</strong>
                    <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo $product['product_brand']; ?>">
                        <?php echo htmlspecialchars($product['brand_name']); ?>
                    </a>
                </p>

                <?php if ($product['product_desc']): ?>
                    <p><?php echo nl2br(htmlspecialchars($product['product_desc'])); ?></p>
                <?php endif; ?>

                <?php if ($product['product_keywords']): ?>
                    <p class="meta-line">
                        <strong>Keywords:</strong> <?php echo htmlspecialchars($product['product_keywords']); ?>
                    </p>
                <?php endif; ?>

                <a class="btn" href="<?php echo BASE_URL; ?>/index.php?add_cart=<?php echo $product['product_id']; ?>">Add to Cart</a>
            </div>
        </div>

        <a class="back-link" href="<?php echo BASE_URL; ?>/index.php">&larr; Back to products</a>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>