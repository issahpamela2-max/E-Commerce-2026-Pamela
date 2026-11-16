<?php
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$cat_id   = isset($_GET['cat']) && is_scalar($_GET['cat']) ? intval($_GET['cat']) : 0;
$brand_id = isset($_GET['brand']) && is_scalar($_GET['brand']) ? intval($_GET['brand']) : 0;
$show_all = isset($_GET['all']) && $_GET['all'] === '1';
$sort_options = [
    'title_asc' => 'Title: A to Z',
    'price_asc' => 'Price: Low to High',
    'price_desc' => 'Price: High to Low',
    'newest' => 'Newest'
];
$sort = $_GET['sort'] ?? 'title_asc';
if (!is_string($sort) || !isset($sort_options[$sort])) {
    $sort = 'title_asc';
}
$page_value = $_GET['page'] ?? 1;
$page = max(1, is_scalar($page_value) ? intval($page_value) : 1);
$page_size = 8;
$catalog_view = $cat_id > 0 || $brand_id > 0 || $show_all;

if ($cat_id > 0) {
    $cat      = $controller->getCategoryById($cat_id);
    $heading  = $cat ? htmlspecialchars($cat['cat_name']) : 'Category';
    $total_products = $controller->countProductsByCategory($cat_id);
} elseif ($brand_id > 0) {
    $brand    = $controller->getBrandById($brand_id);
    $heading  = $brand ? htmlspecialchars($brand['brand_name']) : 'Brand';
    $total_products = $controller->countProductsByBrand($brand_id);
} elseif ($show_all) {
    $heading = 'All Products';
    $total_products = $controller->countAllProducts();
} else {
    $products = $controller->getFeaturedProducts(6);
    $heading  = 'Featured Products';
}

if ($catalog_view) {
    $total_pages = max(1, (int) ceil($total_products / $page_size));
    $page = min($page, $total_pages);
    $offset = ($page - 1) * $page_size;
    if ($cat_id > 0) {
        $products = $controller->getProductsByCategory($cat_id, $page_size, $offset, $sort);
    } elseif ($brand_id > 0) {
        $products = $controller->getProductsByBrand($brand_id, $page_size, $offset, $sort);
    } else {
        $products = $controller->getAllProducts($page_size, $offset, $sort);
    }
}
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<main class="main-content home-content">

    <h2><?php echo $heading; ?></h2>

    <?php if ($catalog_view): ?>
        <form class="catalog-controls" method="GET" action="<?php echo BASE_URL; ?>/index.php">
            <?php if ($cat_id > 0): ?>
                <input type="hidden" name="cat" value="<?php echo $cat_id; ?>">
            <?php elseif ($brand_id > 0): ?>
                <input type="hidden" name="brand" value="<?php echo $brand_id; ?>">
            <?php else: ?>
                <input type="hidden" name="all" value="1">
            <?php endif; ?>
            <label for="catalog-sort">Sort by</label>
            <select id="catalog-sort" name="sort">
                <?php foreach ($sort_options as $value => $label): ?>
                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $sort === $value ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Apply</button>
        </form>
        <p class="meta-line">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
    <?php endif; ?>

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
                    <form method="POST" action="<?php echo BASE_URL; ?>/actions/add_to_cart_action.php">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="add_cart" value="<?php echo intval($p['product_id']); ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($catalog_view && $total_products > 0): ?>
        <?php
        $pagination_base_url = BASE_URL . '/index.php';
        include __DIR__ . '/layout/pagination.php';
        ?>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>