<?php require_once __DIR__ . '/../core/core.php'; ?>
<?php include __DIR__ . '/layout/header.php'; ?>

<h2>Create an Account</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form id="register-form" method="POST" action="<?php echo BASE_URL; ?>/actions/register_action.php">

    <label>Full Name</label>
    <input type="text" name="name" id="name">
    <span class="field-error" id="name-error"></span>

    <label>Email</label>
    <input type="text" name="email" id="email" maxlength="50">
    <span class="field-error" id="email-error"></span>

    <label>Password</label>
    <input type="password" name="pass" id="pass">
    <span class="field-error" id="pass-error"></span>

    <label>Country</label>
    <select name="country" id="country">
        <option value="">-- Select --</option>
        <option value="Ghana">Ghana</option>
        <option value="Nigeria">Nigeria</option>
        <option value="Kenya">Kenya</option>
        <option value="South Africa">South Africa</option>
        <option value="Other">Other</option>
    </select>
    <span class="field-error" id="country-error"></span>

    <label>City</label>
    <input type="text" name="city" id="city">
    <span class="field-error" id="city-error"></span>

    <label>Contact Number</label>
    <input type="text" name="contact" id="contact" maxlength="15">
    <span class="field-error" id="contact-error"></span>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="<?php echo BASE_URL; ?>/views/login.php">Login here</a></p>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/layout/footer.php'; ?>