
<?php
// Main layout for the storefront and account pages, not the admin area.
// core.php should already be loaded before this header.
// Pages may set $pageTitle and $pageDescription before including it.
// Styles: css/theme-luxury.css (loaded last, after the older stylesheets).

require_once __DIR__ . '/../../controllers/CartController.php';

$cartController = new CartController();
$cartCustomerId = is_logged_in() ? get_customer_id() : 0;

/* Guest carts live in the session — always ask the controller.
   Admins don't shop, so their count stays at zero. */
$cartCount = (!(is_logged_in() && is_admin()))
    ? $cartController->getCartCount($cartCustomerId)
    : 0;

$headerSearchQuery = trim($_GET['user_query'] ?? '');
$isAdminViewer = is_logged_in() && is_admin();
// Everyone except admins is treated as a customer here.
$isCustomerViewer = is_logged_in() && !$isAdminViewer;

// Used to mark the active item in the menu below.
$currentPage = basename($_SERVER['PHP_SELF']);

// Main menu. Home décor and bespoke point at the home page sections until
// their own pages exist.
$navItems = [
    ['label' => 'Furniture', 'href' => BASE_URL . 'views/shop.php', 'pages' => ['shop.php', 'single_product.php', 'all_products.php', 'search_results.php']],
    ['label' => 'Home Décor', 'href' => BASE_URL . 'index.php#decor', 'pages' => []],
    ['label' => 'Bespoke', 'href' => BASE_URL . 'index.php#bespoke', 'pages' => []],
    ['label' => 'Our Story', 'href' => BASE_URL . 'views/pages/about.php', 'pages' => ['about.php']],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($pageTitle) && $pageTitle !== '' ? $pageTitle . ' | ' . CC_SITE_NAME : CC_SITE_NAME . ' | Furniture and Home Décor'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?v=20261105-stickyfoot">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/crowncare_additions.css?v=20261109">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/cms-site.css?v=20261116-consult">
<?php require __DIR__ . '/head_assets.php'; ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/theme-luxury.css?v=20261123-1">
</head>

<body>
<a class="cc-skip-link" href="#main-content">Skip to main content</a>
<?php require __DIR__ . '/flash.php'; ?>

<div class="lx-topline">
    <div class="lx-shell">
        <span>Made in our Obuasi workshop · Custom orders welcome</span>
        <span><?php echo e(CC_CONTACT_HOURS); ?></span>
    </div>
</div>

<header class="lx-nav cc-header" id="lx-nav">
    <div class="lx-shell lx-nav-row">

        <div class="lx-nav-left">
            <?php if (!$isAdminViewer): ?>
                <div class="lx-icons">
                    <button type="button" class="lx-menu-btn" aria-label="Open menu" aria-controls="lx-main-nav" aria-expanded="false" data-lx-menu>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
                    </button>
                </div>
                <nav class="lx-links" id="lx-main-nav" aria-label="Main">
                    <?php foreach ($navItems as $item): ?>
                        <?php $isActive = in_array($currentPage, $item['pages'], true); ?>
                        <a
                            href="<?php echo $item['href']; ?>"
                            class="<?php echo $isActive ? 'is-active' : ''; ?>"
                            <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                        ><?php echo e($item['label']); ?></a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>

        <a class="lx-brand" href="<?php echo BASE_URL; ?>index.php">
            <strong><?php echo e(CC_SITE_NAME); ?></strong>
            <span>Obuasi · Ghana</span>
        </a>

        <div class="lx-icons">
            <button type="button" aria-label="Search" aria-controls="lx-search" aria-expanded="<?php echo $headerSearchQuery !== '' ? 'true' : 'false'; ?>" data-lx-search>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
            </button>

            <?php if ($isAdminViewer): ?>
                <a href="<?php echo BASE_URL; ?>views/admin/index.php">Dashboard</a>
                <?php $signoutClass = 'lx-signout'; require __DIR__ . '/signout_form.php'; ?>
            <?php else: ?>
                <a
                    class="lx-hide-sm"
                    href="<?php echo BASE_URL; ?><?php echo is_logged_in() ? 'views/account/my_account.php' : 'views/login.php'; ?>"
                    title="<?php echo is_logged_in() ? 'My account' : 'Sign in or create an account'; ?>"
                    aria-label="<?php echo is_logged_in() ? 'My account' : 'Sign in'; ?>"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>
                </a>

                <a href="<?php echo BASE_URL; ?>views/cart.php" aria-label="Cart, <?php echo (int) $cartCount; ?> items">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
                    <span class="lx-txt">Bag</span>(<span class="cc-cart-count"><?php echo (int) $cartCount; ?></span>)
                </a>

                <?php if ($isCustomerViewer): ?>
                    <span class="lx-hide-sm lx-txt"><?php $signoutClass = 'lx-signout'; require __DIR__ . '/signout_form.php'; ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php /* Search submits to shop.php?user_query=...; the shop page does the filtering. */ ?>
    <div class="lx-search" id="lx-search">
        <form class="lx-shell" action="<?php echo BASE_URL; ?>views/shop.php" method="GET" role="search">
            <label class="visually-hidden" for="header-search-input">Search products</label>
            <input
                id="header-search-input"
                type="search"
                name="user_query"
                placeholder="Search furniture and décor"
                value="<?php echo e($headerSearchQuery); ?>"
            >
            <button type="submit">Search</button>
        </form>
    </div>
</header>
<script>
(function () {
    var nav = document.getElementById('lx-nav');

    // Lets pages size things to the space below the header.
    function setHeight() {
        document.documentElement.style.setProperty('--cc-header-h', nav.offsetHeight + 'px');
    }
    setHeight();
    window.addEventListener('resize', setHeight);

    // Mobile menu and search toggles.
    function toggle(button, className, onOpen) {
        if (!button) { return; }
        button.addEventListener('click', function () {
            var open = nav.classList.toggle(className);
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open && onOpen) { onOpen(); }
        });
    }
    toggle(nav.querySelector('[data-lx-menu]'), 'is-open');
    toggle(nav.querySelector('[data-lx-search]'), 'is-searching', function () {
        document.getElementById('header-search-input').focus();
    });
    <?php if ($headerSearchQuery !== ''): ?>
    nav.classList.add('is-searching');
    <?php endif; ?>
})();
</script>
