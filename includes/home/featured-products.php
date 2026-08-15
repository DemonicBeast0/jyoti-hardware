<?php
require_once __DIR__ . '/../../config/database.php';

$featuredStmt = $pdo->query(
    'SELECT products.id, products.name, products.image, brands.name AS brand_name
     FROM products
     LEFT JOIN brands ON brands.id = products.brand_id
     WHERE products.status = 1 AND products.featured = 1
     ORDER BY products.id DESC
     LIMIT 6'
);
$featuredProducts = $featuredStmt->fetchAll();
?>

<section class="products-section">

<div class="container">

<div class="section-title text-center mb-5">

<span>FEATURED PRODUCTS</span>

<h2>Popular Products</h2>

<p>
Quality products from trusted manufacturers.
</p>

</div>

<div class="row g-4">

<?php foreach($featuredProducts as $product): ?>

<div class="col-lg-4 col-md-6">

<div class="product-card">

<div class="product-image">

<img src="<?= $product['image']; ?>">

</div>

<div class="product-content">

<h5>

<?= $product['name']; ?>

</h5>

<p>

<?= htmlspecialchars($product['brand_name'] ?? ''); ?>

</p>

<div class="product-footer">

<a href="product-details.php?id=<?= $product['id']; ?>"
class="btn btn-dark">

View Details

</a>

<a href="contact.php?product_id=<?= urlencode($product['id']); ?>"
class="btn btn-warning">

Inquire Now

</a>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</section>
