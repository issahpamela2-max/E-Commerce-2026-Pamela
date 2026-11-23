<?php
// Static FAQ page for shoppers. Answers are kept in one array below, so the
// page is just text and there's nothing to maintain in the database.
require_once __DIR__ . '/../../core/core.php';

// PLACEHOLDER answers: confirm delivery areas and policies with the business.
$questions = [
    'Do you make all the products you sell?' =>
        'We make all our furniture in our own workshop in Obuasi. Our home décor (rugs, curtains, mirrors, lighting and accessories) comes from trusted suppliers; each product page shows which is which.',
    'Where do you deliver?' =>
        'We deliver from Obuasi. The delivery areas and charges are shown at checkout.',
    'How can I pay?' =>
        'You pay online with mobile money or a bank card through Paystack. We never see or store your card details.',
    'How do I track my order?' =>
        'Sign in and open My orders. Each order shows whether it has been received, is being prepared, is out for delivery or has been delivered.',
    'What if an item arrives damaged?' =>
        'Send us a photo within 48 hours and we will arrange a repair or replacement. See Delivery and returns for details.',
];

$pageTitle = 'Frequently asked questions';
$pageDescription = 'Answers about ' . CC_SITE_NAME . ' products, delivery, payment and returns.';
require_once __DIR__ . '/../layout/header.php';
?>

<main class="cc-info">
    <a class="cc-back" href="<?php echo BASE_URL; ?>index.php" data-back>Back</a>

    <h1>Frequently asked questions</h1>
    <p class="cc-info-lead">Cannot find your answer? <a href="<?php echo BASE_URL; ?>views/pages/contact.php">Contact us</a>.</p>

    <div class="cc-faq">
        <?php /* Array keys become the <summary> lines; answers sit in collapsible <details>. */ ?>
        <?php foreach ($questions as $question => $answer): ?>
            <details>
                <summary><?php echo e($question); ?></summary>
                <p><?php echo e($answer); ?></p>
            </details>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
