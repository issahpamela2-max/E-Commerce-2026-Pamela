<?php require_once __DIR__ . '/../core/core.php'; ?>
<?php include __DIR__ . '/layout/header.php'; ?>

<h2>Login</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form id="login-form" method="POST" action="<?php echo BASE_URL; ?>/actions/login_action.php">

    <label>Email</label>
    <input type="text" name="email" id="login-email">
    <span class="field-error" id="login-email-error"></span>

    <label>Password</label>
    <input type="password" name="pass" id="login-pass">
    <span class="field-error" id="login-pass-error"></span>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="<?php echo BASE_URL; ?>/views/register.php">Register here</a></p>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/layout/footer.php'; ?>