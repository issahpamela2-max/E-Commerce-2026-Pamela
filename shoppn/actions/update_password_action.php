<?php
require_once __DIR__ . '/../core/core.php';
require_login();
require_once __DIR__ . '/../controllers/CustomerController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/account/my_account.php');
}

$id = $_SESSION['customer_id'];

$current_pass = $_POST['current_pass'] ?? '';
$new_pass     = $_POST['new_pass'] ?? '';
$confirm_pass = $_POST['confirm_pass'] ?? '';

$errors = [];

if ($current_pass === '') {
    $errors[] = 'Please enter your current password.';
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $new_pass)) {
    $errors[] = 'New password must be at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';
}

if ($new_pass !== $confirm_pass) {
    $errors[] = 'New password and confirmation do not match.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect(BASE_URL . '/views/account/my_account.php');
}

$controller = new CustomerController();
$result = $controller->changePassword($id, $current_pass, $new_pass);

if ($result['success']) {
    $_SESSION['success'] = 'Your password has been changed.';
} else {
    $_SESSION['error'] = $result['error'];
}

redirect(BASE_URL . '/views/account/my_account.php');<?php
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
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>My Account</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h3>Your Details</h3>

<form id="profile-form" method="POST" action="<?php echo BASE_URL; ?>/actions/update_profile_action.php">

    <label>Full Name</label>
    <input type="text" name="name" id="profile-name" maxlength="100"
           value="<?php echo htmlspecialchars($customer['customer_name']); ?>">
    <span class="field-error" id="profile-name-error"></span>

    <label>Email</label>
    <input type="text" value="<?php echo htmlspecialchars($customer['customer_email']); ?>" disabled>
    <span class="help-text">Your email cannot be changed.</span>

    <label>Country</label>
    <select name="country" id="profile-country">
        <option value="">-- Select --</option>
        <?php foreach ($countries as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $customer['customer_country'] === $c ? 'selected' : ''; ?>>
                <?php echo $c; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class="field-error" id="profile-country-error"></span>

    <label>City</label>
    <input type="text" name="city" id="profile-city"
           value="<?php echo htmlspecialchars($customer['customer_city']); ?>">
    <span class="field-error" id="profile-city-error"></span>

    <label>Contact Number</label>
    <input type="text" name="contact" id="profile-contact" maxlength="15"
           value="<?php echo htmlspecialchars($customer['customer_contact']); ?>">
    <span class="field-error" id="profile-contact-error"></span>

    <button type="submit">Update Details</button>
</form>

<hr class="section-divider">

<h3>Change Password</h3>

<form id="password-form" method="POST" action="<?php echo BASE_URL; ?>/actions/update_password_action.php">

    <label>Current Password</label>
    <input type="password" name="current_pass" id="current_pass">
    <span class="field-error" id="current_pass-error"></span>

    <label>New Password</label>
    <input type="password" name="new_pass" id="new_pass">
    <span class="field-error" id="new_pass-error"></span>

    <label>Confirm New Password</label>
    <input type="password" name="confirm_pass" id="confirm_pass">
    <span class="field-error" id="confirm_pass-error"></span>

    <button type="submit">Change Password</button>
</form>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>