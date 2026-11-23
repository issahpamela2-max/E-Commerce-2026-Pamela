<?php
// Static About page for shoppers. PLACEHOLDER copy until the business's story is written.
require_once __DIR__ . '/../../core/core.php';

$pageTitle = 'About us';
$pageDescription = CC_SITE_NAME . ' makes furniture in Obuasi, Ghana, and sells home décor from trusted suppliers.';
require_once __DIR__ . '/../layout/header.php';
?>

<main class="cc-info">
    <a class="cc-back" href="<?php echo BASE_URL; ?>index.php" data-back>Back</a>

    <h1>About <?php echo e(CC_SITE_NAME); ?></h1>
    <p class="cc-info-lead">We make furniture in our own workshop in Obuasi, Ghana.</p>

    <p>Our furniture is built by our own team. You can buy pieces from our showroom or online, or ask us to make something to your own design, size, colour and materials.</p>

    <p>We also sell home décor, such as rugs, curtains, mirrors and lighting. We do not make these ourselves: we choose them from trusted suppliers to go with our furniture.</p>

    <p><a class="cc-button" href="<?php echo BASE_URL; ?>views/shop.php">Visit the shop</a></p>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
