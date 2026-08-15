<?php

require '../auth.php';
require '../../config/database.php';

/* ==========================
   FETCH PRODUCTS
========================== */

$perPage = 10;
$requestedPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = $requestedPage && $requestedPage > 0 ? $requestedPage : 1;

$totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

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
ORDER BY products.id ASC
LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<?php if (!$products): ?>

<tr>
    <td colspan="7" class="text-center text-muted py-4">No products found.</td>
</tr>

<?php endif; ?>

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

<?php if ($totalProducts > 0): ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">

    <p class="mb-0 text-muted small">
        Showing <?= $offset + 1; ?>–<?= min($offset + $perPage, $totalProducts); ?> of <?= $totalProducts; ?> products
    </p>

    <?php if ($totalPages > 1): ?>

    <nav aria-label="Products pagination">
        <ul class="pagination mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= max(1, $page - 1); ?>" aria-label="Previous">Previous</a>
            </li>

            <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++): ?>
            <li class="page-item <?= $pageNumber === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $pageNumber; ?>"><?= $pageNumber; ?></a>
            </li>
            <?php endfor; ?>

            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= min($totalPages, $page + 1); ?>" aria-label="Next">Next</a>
            </li>
        </ul>
    </nav>

    <?php endif; ?>

</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>
