<?php
require_once __DIR__ . '/../../core/core.php';
require_login();
require_once __DIR__ . '/../../controllers/CustomerController.php';

$controller = new CustomerController();
$customer = $controller->getCustomerById($_SESSION['customer_id']);

if (!$customer) {
    $_SESSION['error'] = 'Could not load your account.';
    redirect(BASE_URL . '/index.php');
}

$countries = ['Ghana', 'Nigeria', 'Kenya', 'South Africa', 'Other'];
if (!in_array($customer['customer_country'], $countries, true)) {
    array_unshift($countries, $customer['customer_country']);
}
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>My Account</h2>
<p class="account-intro">Update your contact details and manage your sign-in password here.</p>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<nav class="account-shortcuts" aria-label="Account settings">
    <a href="#profile-details">Profile Details</a>
    <a href="#password-settings">Password &amp; Security</a>
</nav>

<section class="account-panel" id="profile-details">
    <h3>Profile Details</h3>

    <form id="profile-form" method="POST" action="<?php echo BASE_URL; ?>/actions/update_profile_action.php">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <label>Full Name</label>
        <input type="text" name="name" id="profile-name" maxlength="100" required
               value="<?php echo htmlspecialchars($customer['customer_name']); ?>">
        <span class="field-error" id="profile-name-error"></span>

        <label>Email</label>
        <input type="email" value="<?php echo htmlspecialchars($customer['customer_email']); ?>" disabled>

        <label>Country</label>
        <select name="country" id="profile-country" required>
            <option value="">-- Select --</option>
            <?php foreach ($countries as $c): ?>
                <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $customer['customer_country'] === $c ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="field-error" id="profile-country-error"></span>

        <label>City</label>
        <input type="text" name="city" id="profile-city" maxlength="30" required
               value="<?php echo htmlspecialchars($customer['customer_city']); ?>">
        <span class="field-error" id="profile-city-error"></span>

        <label>Contact Number</label>
        <input type="text" name="contact" id="profile-contact" maxlength="15" required
               value="<?php echo htmlspecialchars($customer['customer_contact']); ?>">
        <span class="field-error" id="profile-contact-error"></span>

        <button type="submit">Update Details</button>
    </form>
</section>

<section class="account-panel" id="password-settings">
    <h3>Password &amp; Security</h3>
    <p class="help-text">Use your current password to set a new password for this account.</p>

    <form id="password-form" method="POST" action="<?php echo BASE_URL; ?>/actions/update_password_action.php">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

        <label>Current Password</label>
        <input type="password" name="current_pass" id="current_pass" autocomplete="current-password" required>
        <span class="field-error" id="current_pass-error"></span>

        <label>New Password</label>
        <input type="password" name="new_pass" id="new_pass" autocomplete="new-password" minlength="8" required>
        <span class="field-error" id="new_pass-error"></span>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_pass" id="confirm_pass" autocomplete="new-password" minlength="8" required>
        <span class="field-error" id="confirm_pass-error"></span>

        <button type="submit">Change Password</button>
    </form>
</section>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>