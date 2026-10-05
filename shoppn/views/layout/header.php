<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shoppn</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>

<div class="header">
    <h1>Shoppn</h1>

    <div class="nav">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>

       <?php if (is_logged_in() && is_admin()): ?>
            <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Brands</a>
            <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Categories</a>
        <?php endif; ?>

        <?php if (is_logged_in()): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
            <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
            <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/views/register.php">Register</a>
            <a href="<?php echo BASE_URL; ?>/views/login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">