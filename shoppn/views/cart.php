<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$ip = get_ip();

$controller = new CartController();
$items = $controller->getCartItems($ip);
$total = $controller->getCartTotal($ip);
?>
<?php include __DIR__ . '/layout/header.php'; ?>

<h2>Shopping Cart</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($items)): ?>

    <p>Your cart is empty.</p>
    <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>

<?php else: ?>

    <form method="POST" id="cart-form" action="<?php echo BASE_URL; ?>/actions/update_qty_action.php">

        <table class="data-table cart-table">
            <tr>
                <th>Remove</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>

            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <input type="checkbox" name="remove[]" value="<?php echo $item['p_id']; ?>" form="remove-form">
                </td>

                <td class="cart-product">
                    <?php if ($item['product_image']): ?>
                        <img class="thumb"
                             src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>"
                             alt="<?php echo htmlspecialchars($item['product_title']); ?>">
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/views/single_product.php?pro_id=<?php echo $item['p_id']; ?>">
                        <?php echo htmlspecialchars($item['product_title']); ?>
                    </a>
                </td>

                <td>
                    <input type="hidden" name="update[]" value="<?php echo $item['p_id']; ?>">
                    <input type="number" class="qty-input" name="qty[]" min="1"
                           value="<?php echo intval($item['qty']); ?>">
                </td>

                <td>GHS <?php echo number_format($item['product_price'], 2); ?></td>

                <td>GHS <?php echo number_format($item['product_price'] * $item['qty'], 2); ?></td>
            </tr>
            <?php endforeach; ?>

            <tr class="total-row">
                <td colspan="4"><strong>Total</strong></td>
                <td><strong>GHS <?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </table>

        <div class="cart-actions">
            <button type="submit">Update Quantity</button>
            <button type="submit" form="remove-form" class="btn-secondary">Remove from Cart</button>
            <a class="btn" href="<?php echo BASE_URL; ?>/index.php">Continue Shopping</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/views/checkout.php">Checkout</a>
        </div>
    </form>

    <form method="POST" id="remove-form" action="<?php echo BASE_URL; ?>/actions/remove_from_cart_action.php"></form>

<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/layout/footer.php'; ?>