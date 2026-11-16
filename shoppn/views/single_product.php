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

<main class="main-content">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

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
                <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo intval($product['product_cat']); ?>">
                    <?php echo htmlspecialchars($product['cat_name']); ?>
                </a>
            </p>

            <p class="meta-line">
                <strong>Brand:</strong>
                <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo intval($product['product_brand']); ?>">
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

            <form method="POST" action="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="add_cart" value="<?php echo intval($product['product_id']); ?>">
                <button type="submit" class="btn">Add to Cart</button>
            </form>
        </div>
    </div>

    <a class="back-link" href="<?php echo BASE_URL; ?>/index.php">&larr; Back to products</a>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
