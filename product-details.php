<?php

require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

$id = (int)$_GET['id'];

$sql = "
SELECT
    products.*,
    brands.name AS brand_name,
    categories.name AS category_name
FROM products
LEFT JOIN brands
ON products.brand_id = brands.id
LEFT JOIN categories
ON products.category_id = categories.id
WHERE products.id = ?
LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);

$product = $stmt->fetch();

if (!$product) {

    header("Location: products.php");
    exit;
}
?>

<section class="py-5">

<div class="container">

<div class="row">

<div class="col-lg-6">

<img
src="<?= htmlspecialchars($product['image']); ?>"
class="img-fluid rounded shadow">

</div>

<div class="col-lg-6">

<span class="badge bg-warning text-dark mb-3">

<?= htmlspecialchars($product['badge']); ?>

</span>

<h2>

<?= htmlspecialchars($product['name']); ?>

</h2>

<p>

<strong>Brand :</strong>

<?= htmlspecialchars($product['brand_name']); ?>

</p>

<p>

<strong>Category :</strong>

<?= htmlspecialchars($product['category_name']); ?>

</p>

<p>

<strong>Price :</strong>

Rs. <?= number_format($product['price'],2); ?>

</p>

<p>

<?= nl2br(htmlspecialchars($product['description'])); ?>

</p>

<?php if($product['stock']>0): ?>

<span class="badge bg-success">

In Stock

</span>

<?php else: ?>

<span class="badge bg-danger">

Out of Stock

</span>

<?php endif; ?>

<br><br>

<a
href="contact.php?product_id=<?= urlencode($product['id']); ?>"
class="btn btn-warning btn-lg">

Inquire About This Product

</a>

<?php if ($product['stock'] > 0): ?>
<form action="cart-action.php" method="post" class="d-inline">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
    <input type="hidden" name="return_to" value="product-details.php?id=<?= $product['id']; ?>">
    <button type="submit" class="btn btn-outline-dark btn-lg">Add to Cart</button>
</form>
<?php endif; ?>

<a
href="products.php"
class="btn btn-dark btn-lg">

Back to Products

</a>

</div>

</div>

</div>

</section>

<?php

$related = $pdo->prepare("
SELECT *
FROM products
WHERE category_id=?
AND id!=?
LIMIT 4
");

$related->execute([
$product['category_id'],
$product['id']
]);

$relatedProducts = $related->fetchAll();

?>

<section class="py-5 bg-light">

<div class="container">

<h3 class="mb-4">

Related Products

</h3>

<div class="row">

<?php foreach($relatedProducts as $item): ?>

<div class="col-md-3">

<div class="card h-100 shadow-sm">

<img
src="<?= htmlspecialchars($item['image']); ?>"
class="card-img-top"
style="height:200px;object-fit:cover;">

<div class="card-body">

<h6>

<?= htmlspecialchars($item['name']); ?>

</h6>

<a
href="product-details.php?id=<?= $item['id']; ?>"
class="btn btn-dark btn-sm">

View Details

</a>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</section>

<?php include 'includes/footer.php'; ?>
