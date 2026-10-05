<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

// Are we editing? ?edit_id=N
$edit_id  = intval($_GET['edit_id'] ?? 0);
$edit_cat = false;

if ($edit_id > 0) {
    $edit_cat = $controller->getCategoryById($edit_id);
}

$categories = $controller->getAllCategories();
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><?php echo $edit_cat ? 'Edit Category' : 'Add Category'; ?></h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form id="category-form" method="POST"
      action="<?php echo BASE_URL; ?>/actions/<?php echo $edit_cat ? 'update_category_action.php' : 'add_category_action.php'; ?>">

    <?php if ($edit_cat): ?>
        <input type="hidden" name="cat_id" value="<?php echo $edit_cat['cat_id']; ?>">
    <?php endif; ?>

    <label>Category Name</label>
    <input type="text" name="cat_name" id="cat_name" maxlength="100"
           value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['cat_name']) : ''; ?>">
    <span class="field-error" id="cat_name-error"></span>

    <button type="submit"><?php echo $edit_cat ? 'Update Category' : 'Add Category'; ?></button>

    <?php if ($edit_cat): ?>
        <a class="cancel-link" href="<?php echo BASE_URL; ?>/views/admin/category.php">Cancel</a>
    <?php endif; ?>
</form>

<h3>All Categories</h3>

<?php if (empty($categories)): ?>
    <p>No categories yet.</p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?php echo $c['cat_id']; ?></td>
            <td><?php echo htmlspecialchars($c['cat_name']); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/views/admin/category.php?edit_id=<?php echo $c['cat_id']; ?>">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>