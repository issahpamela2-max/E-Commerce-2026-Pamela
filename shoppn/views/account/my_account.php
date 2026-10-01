<?php
require_once __DIR__ . '/../../core/core.php';
require_login();
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2>My Account</h2>

<p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['customer_name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>

<?php include __DIR__ . '/../layout/footer.php'; ?>