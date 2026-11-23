<?php
/*
 * Shared <head> items for every layout (store, admin):
 * form token, favicon, page description and the shared stylesheet.
 * Included by layout/header.php and layout/admin_header.php.
 * Pages may set $pageTitle and
 * $pageDescription before the layout is included.
 */
?>
    <?php /* js/app.js reads this token for AJAX POSTs. */ ?>
    <meta name="csrf-token" content="<?php echo e(cc_csrf_token()); ?>">
    <meta name="description" content="<?php echo e($pageDescription ?? CC_SITE_NAME . ': ' . CC_SITE_TAGLINE); ?>">
    <link rel="icon" href="<?php echo BASE_URL; ?>images/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/app.css?v=20261122-5">
