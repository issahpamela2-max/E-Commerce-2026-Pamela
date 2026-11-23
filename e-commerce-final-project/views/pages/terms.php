<?php
// Static terms of use for shoppers; new accounts have to accept these.
require_once __DIR__ . '/../../core/core.php';

$pageTitle = 'Terms of use';
$pageDescription = 'The terms for using the ' . CC_SITE_NAME . ' website and placing orders.';
require_once __DIR__ . '/../layout/header.php';
?>

<main class="cc-info">
    <a class="cc-back" href="<?php echo BASE_URL; ?>index.php" data-back>Back</a>

    <h1>Terms of use</h1>
    <p class="cc-updated">Last updated 22 November 2026</p>

    <p>These terms apply when you use the <?php echo e(CC_SITE_NAME); ?> website or buy our products. By creating an account or placing an order, you accept them.</p>

    <h2>Your account</h2>
    <p>Keep your password private. You are responsible for orders placed from your account. Tell us straight away if you think someone else has used it.</p>

    <h2>Orders and prices</h2>
    <ul>
        <li>Prices are in Ghana cedis (GH₵) and include all taxes.</li>
        <li>Your order is confirmed once Paystack confirms your payment. We email you a receipt.</li>
        <li>If a product becomes unavailable after you pay, we will contact you and refund it in full.</li>
    </ul>

    <h2>Delivery</h2>
    <p>We deliver from Obuasi. Delivery areas and charges are shown at checkout. Details are on our <a href="<?php echo BASE_URL; ?>views/pages/shipping.php">Delivery and returns</a> page.</p>

    <h2>Returns and refunds</h2>
    <p>Unopened products can be returned within 14 days of delivery. Damaged products are replaced. Full details are on the <a href="<?php echo BASE_URL; ?>views/pages/shipping.php">Delivery and returns</a> page.</p>



    <h2>Using this website</h2>
    <p>Do not misuse the website, for example by trying to get into other people's accounts or by interfering with how it works. The <?php echo e(CC_SITE_NAME); ?> name, photos and text belong to <?php echo e(CC_SITE_NAME); ?>.</p>

    <h2>Law</h2>
    <p>These terms are governed by the laws of Ghana.</p>

    <h2>Contact</h2>
    <?php /* The support email is set in core/site_config.php. */ ?>
    <p>Questions about these terms? Email <a href="mailto:<?php echo e(CC_CONTACT_EMAIL); ?>"><?php echo e(CC_CONTACT_EMAIL); ?></a>.</p>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
