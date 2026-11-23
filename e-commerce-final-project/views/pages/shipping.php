<?php
// Static delivery and returns page for shoppers.
// PLACEHOLDER: confirm delivery areas, times and the returns policy with the business.
require_once __DIR__ . '/../../core/core.php';
$pageTitle = 'Delivery and returns';
$pageDescription = 'How ' . CC_SITE_NAME . ' orders are delivered from Obuasi, and our returns and refunds.';
require_once __DIR__ . '/../layout/header.php';
?>

<main class="cc-info">
    <a class="cc-back" href="<?php echo BASE_URL; ?>index.php" data-back>Back</a>

    <h1>Delivery and returns</h1>
    <p class="cc-info-lead">We deliver from our workshop in Obuasi.</p>

    <h2>Delivery</h2>
    <ul>
        <li>Delivery areas and charges are shown at checkout.</li>
        <li>We contact you to agree a delivery day for large items.</li>
        <li>We email you when your order is out for delivery.</li>
    </ul>

    <h2>Returns</h2>
    <ul>
        <li>If an item arrives damaged, send us a photo within 48 hours and we will arrange a repair or replacement.</li>
        <?php /* The support email is set in core/site_config.php. */ ?>
        <li>To start a return, email <a href="mailto:<?php echo e(CC_CONTACT_EMAIL); ?>"><?php echo e(CC_CONTACT_EMAIL); ?></a> with your order number.</li>
    </ul>

    <h2>Refunds</h2>
    <p>Refunds are made to your original payment method (mobile money or card) within 5 working days of us receiving the return.</p>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
