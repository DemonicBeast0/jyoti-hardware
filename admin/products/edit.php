<?php
require '../auth.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$imageStmt = $pdo->prepare(
    'SELECT id, image FROM product_images WHERE product_id = ? ORDER BY id ASC'
);
$imageStmt->execute([$id]);
$additionalImages = $imageStmt->fetchAll();

$categories = $pdo->query(
    'SELECT id, name FROM categories WHERE status = 1 ORDER BY name'
)->fetchAll();

$brands = $pdo->query(
    'SELECT id, name FROM brands WHERE status = 1 ORDER BY name'
)->fetchAll();

$error = $_GET['error'] ?? '';

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Product</h2>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form action="update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['id']; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id']; ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" class="form-select" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id']; ?>" <?= $product['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" <?= $product['status'] ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?= !$product['status'] ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock']); ?>" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload New Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product URL <small class="text-muted">(optional)</small></label>
                            <input type="url" name="product_url" class="form-control" value="<?= htmlspecialchars($product['product_url'] ?? ''); ?>" placeholder="https://example.com/product">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Add Additional Images <small class="text-muted">(optional)</small></label>
                            <input type="file" name="additional_images[]" class="form-control" accept="image/*" multiple>
                            <small class="form-text text-muted">You can select more than one image.</small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="6" class="form-control"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <p>Current Image:</p>
                            <img src="../../<?= htmlspecialchars($product['image']); ?>" class="img-fluid rounded shadow-sm" style="max-height:200px;">
                        </div>
                        <?php if ($additionalImages): ?>
                            <div class="col-12 mb-3">
                                <p>Additional Images:</p>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php foreach ($additionalImages as $additionalImage): ?>
                                        <label class="text-center">
                                            <img src="../../<?= htmlspecialchars($additionalImage['image']); ?>" class="img-fluid rounded shadow-sm d-block mb-1" style="height:120px;width:120px;object-fit:cover;">
                                            <input type="checkbox" name="remove_image_ids[]" value="<?= $additionalImage['id']; ?>">
                                            Remove
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i> Update Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
