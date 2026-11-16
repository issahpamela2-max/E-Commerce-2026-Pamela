<?php
require_once __DIR__ . '/../../controllers/CartController.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

$header_cart  = new CartController();
$header_ip    = get_ip();
$header_count = $header_cart->getCartCount($header_ip, get_cart_customer_id());

$catalog_controller = isset($controller) && $controller instanceof ProductController
    ? $controller
    : new ProductController();
$header_categories = $catalog_controller->getAllCategories();
$header_brands     = $catalog_controller->getAllBrands();
$selected_category = intval($_GET['cat'] ?? 0);
$selected_brand    = intval($_GET['brand'] ?? 0);
$header_search_query = isset($_GET['user_query']) && is_string($_GET['user_query'])
    ? $_GET['user_query']
    : '';
$is_home            = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php';
$featured_categories = array_slice($header_categories, 0, 6);
$more_categories = array_slice($header_categories, 6);
$more_category_is_current = false;
foreach ($more_categories as $category) {
    if ($selected_category === intval($category['cat_id'])) {
        $more_category_is_current = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>

<header class="header">
    <div class="header-top">
        <a class="brand-link" href="<?php echo BASE_URL; ?>/index.php">Shoppn</a>

        <details class="header-dropdown category-dropdown">
            <summary class="header-dropdown-trigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span>Categories</span>
            </summary>
            <div class="dropdown-panel catalog-panel">
                <section>
                    <h2>Shop by category</h2>
                    <div class="dropdown-list">
                        <a href="<?php echo BASE_URL; ?>/index.php?all=1">All products</a>
                        <?php foreach ($header_categories as $category): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo intval($category['cat_id']); ?>">
                                <?php echo htmlspecialchars($category['cat_name']); ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($header_categories)): ?>
                            <span class="dropdown-empty">Categories will appear here when available.</span>
                        <?php endif; ?>
                    </div>
                </section>
                <section>
                    <h2>Shop by brand</h2>
                    <div class="dropdown-list">
                        <?php foreach ($header_brands as $brand): ?>
                            <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo intval($brand['brand_id']); ?>">
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($header_brands)): ?>
                            <span class="dropdown-empty">Brands will appear here when available.</span>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </details>

        <form class="search-bar" method="GET" action="<?php echo BASE_URL; ?>/views/search_results.php" role="search">
            <input type="search" name="user_query" aria-label="Search products"
                   placeholder="Search products..."
                   value="<?php echo htmlspecialchars($header_search_query, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" aria-label="Search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="6.8"></circle>
                    <path d="m16 16 5 5"></path>
                </svg>
            </button>
        </form>

        <div class="header-actions">
            <?php if (is_logged_in()): ?>
                <details class="header-dropdown account-dropdown">
                    <summary class="header-dropdown-trigger account-trigger">
                        <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                    </summary>
                    <div class="dropdown-panel account-menu-panel">
                        <?php if (is_admin()): ?>
                            <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php">Admin dashboard</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/product.php">Products</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Brands</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Categories</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/orders.php">Orders</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/customers.php">Customers</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/views/account/dashboard.php">Dashboard</a>
                            <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
                            <a href="<?php echo BASE_URL; ?>/views/account/orders.php">My Orders</a>
                        <?php endif; ?>
                        <?php if (is_admin()): ?>
                            <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
                        <?php endif; ?>
                        <form class="account-logout" method="POST" action="<?php echo BASE_URL; ?>/logout.php">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </details>
            <?php else: ?>
                <a class="sign-in-link" href="<?php echo BASE_URL; ?>/views/login.php">Sign in</a>
                <a class="register-link" href="<?php echo BASE_URL; ?>/views/register.php">Create account</a>
            <?php endif; ?>

            <a class="cart-link" href="<?php echo BASE_URL; ?>/views/cart.php"
               aria-label="Shopping cart, <?php echo $header_count; ?> <?php echo $header_count === 1 ? 'item' : 'items'; ?>">
                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span>Cart</span>
                <?php if ($header_count > 0): ?>
                    <span class="cart-badge"><?php echo $header_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <nav class="shop-tabs" aria-label="Shop by category and brand">
        <a class="shop-tab<?php echo $is_home && $selected_category === 0 && $selected_brand === 0 ? ' shop-tab--active' : ''; ?>"
           href="<?php echo BASE_URL; ?>/index.php?all=1"
           <?php echo $is_home && $selected_category === 0 && $selected_brand === 0 ? 'aria-current="page"' : ''; ?>>
            All products
        </a>
        <?php foreach ($featured_categories as $category): ?>
            <?php $category_is_current = $selected_category === intval($category['cat_id']); ?>
            <a class="shop-tab<?php echo $category_is_current ? ' shop-tab--active' : ''; ?>"
               href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo intval($category['cat_id']); ?>"
               <?php echo $category_is_current ? 'aria-current="page"' : ''; ?>>
                <?php echo htmlspecialchars($category['cat_name']); ?>
            </a>
        <?php endforeach; ?>

        <?php if (!empty($more_categories)): ?>
            <details class="shop-menu">
                <summary class="<?php echo $more_category_is_current ? 'shop-tab--active' : ''; ?>">More categories</summary>
                <div class="dropdown-panel shop-menu-panel">
                    <?php foreach ($more_categories as $category): ?>
                        <?php $category_is_current = $selected_category === intval($category['cat_id']); ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo intval($category['cat_id']); ?>"
                           <?php echo $category_is_current ? 'aria-current="page"' : ''; ?>>
                            <?php echo htmlspecialchars($category['cat_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endif; ?>

        <details class="shop-menu brand-filter">
            <summary class="<?php echo $selected_brand > 0 ? 'shop-tab--active' : ''; ?>">Brands</summary>
            <div class="dropdown-panel shop-menu-panel">
                <?php if (empty($header_brands)): ?>
                    <span class="dropdown-empty">Brands will appear here when available.</span>
                <?php else: ?>
                    <?php foreach ($header_brands as $brand): ?>
                        <?php $brand_is_current = $selected_brand === intval($brand['brand_id']); ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo intval($brand['brand_id']); ?>"
                           <?php echo $brand_is_current ? 'aria-current="page"' : ''; ?>>
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </details>
    </nav>
</header>

<div class="container">
