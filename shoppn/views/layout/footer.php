</div>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a class="footer-logo" href="<?php echo BASE_URL; ?>/index.php">Shoppn</a>
            <p>Find your next favorite. Shop online with Shoppn.</p>
        </div>

        <nav class="footer-links" aria-label="Shopping links">
            <h2>Shop</h2>
            <a href="<?php echo BASE_URL; ?>/index.php?all=1">Browse products</a>
            <a href="<?php echo BASE_URL; ?>/views/cart.php">Shopping cart</a>
        </nav>

        <nav class="footer-links" aria-label="Account links">
            <h2>Your account</h2>
            <?php if (is_logged_in()): ?>
                <?php if (is_admin()): ?>
                    <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php">Admin dashboard</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/views/account/dashboard.php">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/views/account/orders.php">Order history</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">Account settings</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/views/login.php">Sign in</a>
                <a href="<?php echo BASE_URL; ?>/views/register.php">Create an account</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Shoppn. All rights reserved.</p>
        <p>Prices are shown in Ghana cedis (GHS).</p>
    </div>
</footer>

</body>
</html>