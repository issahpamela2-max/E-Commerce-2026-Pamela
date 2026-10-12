<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebar_controller = new ProductController();
$sidebar_categories = $sidebar_controller->getAllCategories();
$sidebar_brands     = $sidebar_controller->getAllBrands();
?>

<div class="sidebar">

    <h3>Categories</h3>
    <?php if (empty($sidebar_categories)): ?>
        <p class="sidebar-empty">None yet</p>
    <?php else: ?>
        <ul class="sidebar-list">
            <?php foreach ($sidebar_categories as $c): ?>
                <li>
                    <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo $c['cat_id']; ?>">
                        <?php echo htmlspecialchars($c['cat_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Brands</h3>
    <?php if (empty($sidebar_brands)): ?>
        <p class="sidebar-empty">None yet</p>
    <?php else: ?>
        <ul class="sidebar-list">
            <?php foreach ($sidebar_brands as $b): ?>
                <li>
                    <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo $b['brand_id']; ?>">
                        <?php echo htmlspecialchars($b['brand_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>