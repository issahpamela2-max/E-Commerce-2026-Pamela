<?php
require_once __DIR__ . '/../../controllers/CartController.php';

$header_cart  = new CartController();
$header_ip    = get_ip();
$header_count = $header_cart->getCartCount($header_ip);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>

<div class="header">
    <h1>Shoppn</h1>

    <div class="nav">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>

        <?php if (is_logged_in() && is_admin()): ?>
            <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Brands</a>
            <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Categories</a>
            <a href="<?php echo BASE_URL; ?>/views/admin/product.php">Products</a>
        <?php endif; ?>

        <div class="nav-right">
            <?php if (is_logged_in()): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/views/register.php">Register</a>
                <a href="<?php echo BASE_URL; ?>/views/login.php">Login</a>
            <?php endif; ?>

            <a class="cart-link" href="<?php echo BASE_URL; ?>/views/cart.php">
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

    <form class="search-bar" method="GET" action="<?php echo BASE_URL; ?>/views/search_results.php">
        <input type="text" name="user_query" placeholder="Search products..."
               value="<?php echo isset($_GET['user_query']) ? htmlspecialchars($_GET['user_query']) : ''; ?>">
        <button type="submit">Search</button>
    </form>
</div>

<div class="container">