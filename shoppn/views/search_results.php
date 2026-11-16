<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$raw_query = $_GET['user_query'] ?? '';
$query = is_string($raw_query) ? trim(strip_tags($raw_query)) : '';

$controller = new ProductController();
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
$total_products = $query === '' ? 0 : $controller->countSearchProducts($query);
$total_pages = max(1, (int) ceil($total_products / $page_size));
$page = min($page, $total_pages);
$products = ($query === '') ? [] : $controller->searchProducts(
    $query,
    $page_size,
    ($page - 1) * $page_size,
    $sort
);
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<main class="main-content">
    <h2>Search Results</h2>

    <?php if ($query === ''): ?>
        <p>Please enter something to search for.</p>
    <?php elseif (empty($products)): ?>
        <p>No products found for "<?php echo htmlspecialchars($query); ?>".</p>
    <?php else: ?>
        <p class="meta-line">
            Showing <?php echo count($products); ?> of <?php echo $total_products; ?> result<?php echo $total_products === 1 ? '' : 's'; ?>
            for "<?php echo htmlspecialchars($query); ?>"
        </p>

        <form class="catalog-controls" method="GET" action="<?php echo BASE_URL; ?>/views/search_results.php">
            <input type="hidden" name="user_query" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="search-sort">Sort by</label>
            <select id="search-sort" name="sort">
                <?php foreach ($sort_options as $value => $label): ?>
                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $sort === $value ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Apply</button>
        </form>

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
        <?php
        $pagination_base_url = BASE_URL . '/views/search_results.php';
        include __DIR__ . '/layout/pagination.php';
        ?>
    <?php endif; ?>

    <a class="back-link" href="<?php echo BASE_URL; ?>/index.php?all=1">&larr; Browse all products</a>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
