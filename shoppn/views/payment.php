<?php
require_once __DIR__ . '/../core/core.php';
require_login(BASE_URL . '/views/payment.php');
require_once __DIR__ . '/../core/paystack_cred.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

$ip = get_ip();
$customer_id = get_cart_customer_id();

$controller = new CartController();
$items = $controller->getCartItems($ip, $customer_id);
$total = $controller->getCartTotal($ip, $customer_id);
$customerController = new CustomerController();
$customer = $customerController->getCustomerById($_SESSION['customer_id']);

if (!$customer) {
    $_SESSION['error'] = 'Could not load your account details. Please try again.';
    redirect(BASE_URL . '/views/cart.php');
}

// Nothing to pay for
if (empty($items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(BASE_URL . '/views/cart.php');
}
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<h2>Checkout</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h3>Order Summary</h3>

<table class="data-table">
    <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Subtotal</th>
    </tr>

    <?php foreach ($items as $item): ?>
    <tr>
        <td><?php echo htmlspecialchars($item['product_title']); ?></td>
        <td><?php echo intval($item['qty']); ?></td>
        <td>GHS <?php echo number_format($item['product_price'], 2); ?></td>
        <td>GHS <?php echo number_format($item['product_price'] * $item['qty'], 2); ?></td>
    </tr>
    <?php endforeach; ?>

    <tr class="total-row">
        <td colspan="3"><strong>Total</strong></td>
        <td><strong>GHS <?php echo number_format($total, 2); ?></strong></td>
    </tr>
</table>

<div class="checkout-details">
    <h3>Delivery and Contact Details</h3>
    <p class="meta-line"><strong>Name:</strong> <?php echo htmlspecialchars($customer['customer_name']); ?></p>
    <p class="meta-line"><strong>Email:</strong> <?php echo htmlspecialchars($customer['customer_email']); ?></p>
    <p class="meta-line"><strong>Contact:</strong> <?php echo htmlspecialchars($customer['customer_contact']); ?></p>
    <p class="meta-line"><strong>City:</strong> <?php echo htmlspecialchars($customer['customer_city']); ?></p>
    <p class="meta-line"><strong>Country:</strong> <?php echo htmlspecialchars($customer['customer_country']); ?></p>
    <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">Update account details</a>
</div>

<div class="cart-actions">
    <button type="button" id="pay-button">Pay GHS <?php echo number_format($total, 2); ?></button>
    <a class="btn" href="<?php echo BASE_URL; ?>/views/cart.php">Back to Cart</a>
</div>

<div id="payment-provider-error" class="error-box" role="alert" hidden>
    Payment is temporarily unavailable. Please try again later or contact support.
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
var payButton = document.getElementById('pay-button');
var paymentError = document.getElementById('payment-provider-error');
var paystackPublicKey = <?php echo json_encode(PAYSTACK_PUBLIC_KEY); ?>;
var validPublicKey = /^pk_(test|live)_[A-Za-z0-9]+$/.test(paystackPublicKey)
    && !/x{4,}/i.test(paystackPublicKey);

if (typeof PaystackPop === 'undefined' || !validPublicKey) {
    payButton.disabled = true;
    paymentError.textContent = typeof PaystackPop === 'undefined'
        ? 'The payment service could not be loaded. Check your internet connection and try again.'
        : 'Online payments are not configured. Please contact the store administrator.';
    paymentError.hidden = false;
} else {
    payButton.addEventListener('click', function () {
        paymentError.hidden = true;

        var handler = PaystackPop.setup({
            key: paystackPublicKey,
            email: <?php echo json_encode($customer['customer_email']); ?>,
            amount: <?php echo intval(round($total * 100)); ?>,
            currency: 'GHS',

            onSuccess: function (response) {
                if (!response || !response.reference) {
                    paymentError.hidden = false;
                    return;
                }
                window.location = <?php echo json_encode(BASE_URL . '/actions/process_payment_action.php?ref='); ?> + encodeURIComponent(response.reference);
            },

            onCancel: function () {
                paymentError.textContent = 'Payment was cancelled. Your cart is unchanged.';
                paymentError.hidden = false;
            }
        });

        handler.openIframe();
    });
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>