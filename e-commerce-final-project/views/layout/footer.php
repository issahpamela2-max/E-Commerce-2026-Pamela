<?php /* Site footer for the storefront and account pages (everything that includes layout/header.php). Links change with who is signed in. Styles: css/theme-luxury.css. */ ?>
<footer class="lx-footer">
    <div class="lx-shell">
        <div class="lx-foot-cols">
            <div>
                <a class="lx-brand" href="<?php echo BASE_URL; ?>index.php">
                    <strong><?php echo e(CC_SITE_NAME); ?></strong>
                    <span>Obuasi · Ghana</span>
                </a>
                <p><?php echo e(CC_SITE_TAGLINE); ?></p>
            </div>

            <div>
                <h3>Shop</h3>
                <a href="<?php echo BASE_URL; ?>views/shop.php">All furniture</a>
                <a href="<?php echo BASE_URL; ?>index.php#decor">Home décor</a>
                <a href="<?php echo BASE_URL; ?>index.php#bespoke">Bespoke furniture</a>
                <?php /* Admins don't shop, so no cart link for them. */ ?>
                <?php if (!is_admin()): ?>
                    <a href="<?php echo BASE_URL; ?>views/cart.php">Your bag</a>
                <?php endif; ?>
            </div>

            <div>
                <h3>Account</h3>
                <?php /* Admin gets a dashboard, customer their account links, guest sign in/register. */ ?>
                <?php if (is_logged_in() && is_admin()): ?>
                    <a href="<?php echo BASE_URL; ?>views/admin/index.php">Dashboard</a>
                    <span class="lx-foot-item"><?php $signoutClass = 'lx-signout'; require __DIR__ . '/signout_form.php'; ?></span>
                <?php elseif (is_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>views/account/my_account.php">My account</a>
                    <a href="<?php echo BASE_URL; ?>views/account/orders.php">My orders</a>
                    <span class="lx-foot-item"><?php $signoutClass = 'lx-signout'; require __DIR__ . '/signout_form.php'; ?></span>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>views/login.php">Sign in</a>
                    <a href="<?php echo BASE_URL; ?>views/register.php">Create an account</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>views/pages/faq.php">FAQ</a>
                <a href="<?php echo BASE_URL; ?>views/pages/shipping.php">Delivery &amp; returns</a>
            </div>

            <div>
                <h3>Visit</h3>
                <span class="lx-foot-item"><?php echo e(CC_STUDIO_LOCATION); ?></span>
                <a href="tel:<?php echo e(preg_replace('/\s+/', '', CC_CONTACT_PHONE)); ?>"><?php echo e(CC_CONTACT_PHONE); ?></a>
                <a href="mailto:<?php echo e(CC_CONTACT_EMAIL); ?>"><?php echo e(CC_CONTACT_EMAIL); ?></a>
                <a href="<?php echo BASE_URL; ?>views/pages/contact.php">Contact us</a>
            </div>
        </div>

        <div class="lx-legal">
            <span>&copy; <?php echo date('Y'); ?> <?php echo e(CC_SITE_NAME); ?>. All rights reserved.</span>
            <nav aria-label="Legal">
                <a href="<?php echo BASE_URL; ?>views/pages/about.php">About</a>
                <a href="<?php echo BASE_URL; ?>views/pages/privacy.php">Privacy policy</a>
                <a href="<?php echo BASE_URL; ?>views/pages/terms.php">Terms of use</a>
            </nav>
        </div>
    </div>
</footer>

<?php /* Shared scripts (flash messages, form handling, etc.) live here. */ ?>
<?php require __DIR__ . '/foot_scripts.php'; ?>
</body>
</html>
