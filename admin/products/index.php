<?php

require '../auth.php';
require '../../config/database.php';

/* ==========================
   FETCH PRODUCTS
========================== */

$sql = "
SELECT
    products.*,
    categories.name AS category_name,
    brands.name AS brand_name
FROM products
LEFT JOIN categories
ON products.category_id = categories.id
LEFT JOIN brands
ON products.brand_id = brands.id
ORDER BY products.id DESC
";

$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';

?>

<div class="content">

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Products Management</h2>

<a href="create.php" class="btn btn-warning">

<i class="fas fa-plus"></i>

Add Product

</a>

</div>

<div class="card shadow-sm">

<div class="card-body">

<table class="table table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Image</th>

<th>Product</th>

<th>Category</th>

<th>Brand</th>

<th>Status</th>

<th width="180">Actions</th>

</tr>

</thead>

<tbody>

<?php foreach($products as $product): ?>

<tr>

<td><?= $product['id']; ?></td>

<td>

<img
src="../../<?= htmlspecialchars($product['image']); ?>"
width="70"
class="rounded">

</td>

<td>

<strong>

<?= htmlspecialchars($product['name']); ?>

</strong>

</td>

<td>

<?= htmlspecialchars($product['category_name']); ?>

</td>

<td>

<?= htmlspecialchars($product['brand_name']); ?>

</td>

<td>

<?php if($product['status']): ?>

<span class="badge bg-success">

Active

</span>

<?php else: ?>

<span class="badge bg-danger">

Inactive

</span>

<?php endif; ?>

</td>

<td>

<a
href="edit.php?id=<?= $product['id']; ?>"
class="btn btn-sm btn-primary">

<i class="fas fa-edit"></i>

</a>

<a
href="delete.php?id=<?= $product['id']; ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this product?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>