<?php
require_once __DIR__ . '/../core/core.php';
require_login();
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<h2>Payment Unsuccessful</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php else: ?>
    <div class="error-box">Your payment could not be completed.</div>
<?php endif; ?>

<p>Your cart has not been changed. You can try again or go back and review your items.</p>

<div class="cart-actions">
    <a class="btn" href="<?php echo BASE_URL; ?>/views/payment.php">Try Again</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>