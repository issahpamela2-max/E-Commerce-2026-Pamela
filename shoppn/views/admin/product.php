<?php
require_once __DIR__ . '/../../core/core.php';
require_admin();
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();

$edit_id      = intval($_GET['edit_id'] ?? 0);
$edit_product = false;

if ($edit_id > 0) {
    $edit_product = $controller->getProductById($edit_id);
}

$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();
?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<h2><?php echo $edit_product ? 'Edit Product' : 'Add Product'; ?></h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="success-box"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-box"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (empty($categories) || empty($brands)): ?>
    <div class="error-box">
        You need at least one brand and one category before adding products.
    </div>
<?php else: ?>

<form id="product-form" method="POST" enctype="multipart/form-data"
      action="<?php echo BASE_URL; ?>/actions/<?php echo $edit_product ? 'update_product_action.php' : 'add_product_action.php'; ?>">

    <?php if ($edit_product): ?>
        <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_product['product_image']); ?>">
    <?php endif; ?>

    <label>Product Title</label>
    <input type="text" name="product_title" id="product_title" maxlength="200"
           value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_title']) : ''; ?>">
    <span class="field-error" id="product_title-error"></span>

    <label>Category</label>
    <select name="product_cat" id="product_cat">
        <option value="">-- Select --</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['cat_id']; ?>"
                <?php echo ($edit_product && $edit_product['product_cat'] == $c['cat_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c['cat_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class="field-error" id="product_cat-error"></span>

    <label>Brand</label>
    <select name="product_brand" id="product_brand">
        <option value="">-- Select --</option>
        <?php foreach ($brands as $b): ?>
            <option value="<?php echo $b['brand_id']; ?>"
                <?php echo ($edit_product && $edit_product['product_brand'] == $b['brand_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($b['brand_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class="field-error" id="product_brand-error"></span>

    <label>Price (GHS)</label>
    <input type="number" name="product_price" id="product_price" step="0.01" min="0"
           value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_price']) : ''; ?>">
    <span class="field-error" id="product_price-error"></span>

    <label>Description</label>
    <textarea name="product_desc" id="product_desc" maxlength="500"><?php echo $edit_product ? htmlspecialchars($edit_product['product_desc']) : ''; ?></textarea>
    <span class="field-error" id="product_desc-error"></span>

    <label>Keywords</label>
    <input type="text" name="product_keywords" id="product_keywords" maxlength="100"
           value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_keywords']) : ''; ?>">
    <span class="help-text">Comma-separated words shoppers might search for.</span>
    <span class="field-error" id="product_keywords-error"></span>

    <?php if ($edit_product && $edit_product['product_image']): ?>
        <label>Current Image</label>
        <img class="preview-image"
             src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($edit_product['product_image']); ?>"
             alt="<?php echo htmlspecialchars($edit_product['product_title']); ?>">
    <?php endif; ?>

    <label>Product Image</label>
    <input type="file" name="product_image" id="product_image" accept="image/*">
    <span class="help-text">
        <?php echo $edit_product ? 'Leave empty to keep the current image.' : 'JPG, PNG, GIF or WEBP. Max 2MB.'; ?>
    </span>
    <span class="field-error" id="product_image-error"></span>

    <button type="submit"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>

    <?php if ($edit_product): ?>
        <a class="cancel-link" href="<?php echo BASE_URL; ?>/views/admin/product.php">Cancel</a>
    <?php endif; ?>
</form>

<?php endif; ?>

<h3>All Products</h3>

<?php if (empty($products)): ?>
    <p>No products yet.</p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <td>
                <?php if ($p['product_image']): ?>
                    <img class="thumb"
                         src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>"
                         alt="<?php echo htmlspecialchars($p['product_title']); ?>">
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($p['product_title']); ?></td>
            <td><?php echo htmlspecialchars($p['cat_name']); ?></td>
            <td><?php echo htmlspecialchars($p['brand_name']); ?></td>
            <td>GHS <?php echo number_format($p['product_price'], 2); ?></td>
            <td>
                <a href="<?php echo BASE_URL; ?>/views/admin/product.php?edit_id=<?php echo $p['product_id']; ?>">Edit</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<script src="<?php echo BASE_URL; ?>/js/validate.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>