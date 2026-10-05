<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

// Are we editing? ?edit_id=N
$edit_id   = intval($_GET['edit_id'] ?? 0);
$edit_brand = false;

if ($edit_id > 0) {
    $edit_brand = $controller->getBrandById($edit_id);
}

$brands = $controller->getAllBrands();
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><?php echo $edit_brand ? 'Edit Brand' : 'Add Brand'; ?></h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form id="brand-form" method="POST"
      action="<?php echo BASE_URL; ?>/actions/<?php echo $edit_brand ? 'update_brand_action.php' : 'add_brand_action.php'; ?>">

    <?php if ($edit_brand): ?>
        <input type="hidden" name="brand_id" value="<?php echo $edit_brand['brand_id']; ?>">
    <?php endif; ?>

    <label>Brand Name</label>
    <input type="text" name="brand_name" id="brand_name" maxlength="100"
           value="<?php echo $edit_brand ? htmlspecialchars($edit_brand['brand_name']) : ''; ?>">
    <span class="field-error" id="brand_name-error"></span>

    <button type="submit"><?php echo $edit_brand ? 'Update Brand' : 'Add Brand'; ?></button>

    <?php if ($edit_brand): ?>
        <a class="cancel-link" href="<?php echo BASE_URL; ?>/views/admin/brand.php">Cancel</a>
    <?php endif; ?>
</form>

<h3>All Brands</h3>

<?php if (empty($brands)): ?>
    <p>No brands yet.</p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Brand Name</th>
            <th>Action</th>
        </tr>
        <?php foreach ($brands as $b): ?>
        <tr>
            <td><?php echo $b['brand_id']; ?></td>
            <td><?php echo htmlspecialchars($b['brand_name']); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/views/admin/brand.php?edit_id=<?php echo $b['brand_id']; ?>">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>