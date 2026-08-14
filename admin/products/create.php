<?php

require '../auth.php';
require '../../config/database.php';

$categories = $pdo->query("
SELECT id,name
FROM categories
WHERE status=1
ORDER BY name
")->fetchAll();

$brands = $pdo->query("
SELECT id,name
FROM brands
WHERE status=1
ORDER BY name
")->fetchAll();

$error = $_GET['error'] ?? '';

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';

?>

<div class="content">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Add Product</h2>

<a href="index.php" class="btn btn-secondary">

Back

</a>

</div>

<div class="card shadow">

<div class="card-body">

<?php if ($error): ?>

    <div class="alert alert-danger">

        <?= htmlspecialchars($error); ?>

    </div>

<?php endif; ?>

<form
action="store.php"
method="POST"
enctype="multipart/form-data">

<div class="row">

    <!-- Product Name -->

    <div class="col-md-6 mb-3">

        <label class="form-label">Product Name</label>

        <input
            type="text"
            name="name"
            class="form-control"
            required>

    </div>

    <!-- Price -->

    <div class="col-md-6 mb-3">

        <label class="form-label">Price</label>

        <input
            type="number"
            step="0.01"
            name="price"
            class="form-control"
            required>

    </div>

    <!-- Category -->

    <div class="col-md-6 mb-3">

        <label class="form-label">Category</label>

        <select
            name="category_id"
            class="form-select"
            required>

            <option value="">Select Category</option>

            <?php foreach($categories as $category): ?>

            <option value="<?= $category['id']; ?>">

                <?= htmlspecialchars($category['name']); ?>

            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <!-- Brand -->

    <div class="col-md-6 mb-3">

        <label class="form-label">Brand</label>

        <select
            name="brand_id"
            class="form-select"
            required>

            <option value="">Select Brand</option>

            <?php foreach($brands as $brand): ?>

            <option value="<?= $brand['id']; ?>">

                <?= htmlspecialchars($brand['name']); ?>

            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <!-- Status -->

    <div class="col-md-6 mb-3">

        <label class="form-label">Status</label>

        <select
            name="status"
            class="form-select">

            <option value="1">Active</option>

            <option value="0">Inactive</option>

        </select>

    </div>

    <!-- Image -->

    <div class="col-12 mb-3">

        <label class="form-label">Product Image</label>

        <input
            type="file"
            name="image"
            class="form-control"
            accept="image/*"
            required>

    </div>

    <!-- Optional Additional Images -->

    <div class="col-12 mb-3">

        <label class="form-label">Additional Images <small class="text-muted">(optional)</small></label>

        <input
            type="file"
            name="additional_images[]"
            class="form-control"
            accept="image/*"
            multiple>

        <small class="form-text text-muted">You can select more than one image.</small>

    </div>

    <!-- Description -->

    <div class="col-12 mb-4">

        <label class="form-label">Description</label>

        <textarea
            name="description"
            rows="6"
            class="form-control"></textarea>

    </div>

</div>

<button
    type="submit"
    class="btn btn-warning">

    <i class="fas fa-save me-2"></i>

    Save Product

</button>

<a
    href="index.php"
    class="btn btn-secondary">

    Cancel

</a>

</form>

</div>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>
