<?php
require_once "config/database.php";

include "includes/header.php";
include "includes/navbar.php";

/* ==========================================
   GET FILTER VALUES
========================================== */

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$brand = trim($_GET["brand"] ?? "");

/* ==========================================
   PRODUCT QUERY
========================================== */

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
WHERE products.status = 1
";

$params = [];

/* Search */

if ($search != "") {
    $sql .= " AND (
        products.name LIKE ?
        OR brands.name LIKE ?
    )";

    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

/* Category */

if ($category != "") {
    $sql .= " AND products.category_id = ?";

    $params[] = $category;
}

/* Brand */

if ($brand != "") {
    $sql .= " AND products.brand_id = ?";

    $params[] = $brand;
}

$sql .= " ORDER BY products.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();

/* ==========================================
   LOAD CATEGORIES
========================================== */

$categoryStmt = $pdo->query("
SELECT id,name
FROM categories
WHERE status=1
ORDER BY name ASC
");

$categories = $categoryStmt->fetchAll();

/* ==========================================
   LOAD BRANDS
========================================== */

$brandStmt = $pdo->query("
SELECT id,name
FROM brands
WHERE status=1
ORDER BY name ASC
");

$brands = $brandStmt->fetchAll();
?>

<!-- ==========================================
PAGE HEADER
========================================== -->

<section class="page-header py-5 bg-dark text-white">

    <div class="container text-center">

        <h1 class="fw-bold display-5">

            Our Products

        </h1>

        <p>

            Home / Products

        </p>

    </div>

</section>

<!-- ==========================================
PRODUCTS
========================================== -->

<section class="products-page py-5">

<div class="container">

<div class="row">

<!-- ==========================================
SIDEBAR
========================================== -->

<div class="col-lg-3 mb-4">

<div class="card shadow-sm border-0">

<div class="card-body">

<h4 class="mb-4">

Search Products

</h4>

<form action="products.php" method="GET">

<div class="mb-3">

<label class="form-label">

Keyword

</label>

<input
type="text"
name="search"
class="form-control"
placeholder="Search..."
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="mb-3">

<label class="form-label">

Category

</label>

<select
class="form-select"
name="category">

<option value="">

All Categories

</option>

<?php foreach ($categories as $cat): ?>

<option
value="<?= $cat["id"] ?>"
<?= $category == $cat["id"] ? "selected" : "" ?>>

<?= htmlspecialchars($cat["name"]) ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-4">

<label class="form-label">

Brand

</label>

<select
class="form-select"
name="brand">

<option value="">

All Brands

</option>

<?php foreach ($brands as $b): ?>

<option
value="<?= $b["id"] ?>"
<?= $brand == $b["id"] ? "selected" : "" ?>>

<?= htmlspecialchars($b["name"]) ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="d-grid gap-2">

<button class="btn btn-warning">

<i class="fas fa-search me-2"></i>

Search

</button>

<a
href="products.php"
class="btn btn-outline-secondary">

Reset Filters

</a>

</div>

</form>

</div>

</div>

</div>

<!-- ==========================================
PRODUCT GRID
========================================== -->

<div class="col-lg-9">

<div class="d-flex justify-content-between align-items-center mb-4">

<h4>

<?= count($products) ?>

Products Found

</h4>

</div>

<div class="row g-4">

<?php if (count($products)): ?>

<?php foreach ($products as $product): ?>

<div class="col-lg-4 col-md-6">

    <div class="card product-card shadow-sm border-0 h-100">

        <div class="position-relative">

            <img
                src="<?= htmlspecialchars($product["image"]) ?>"
                alt="<?= htmlspecialchars($product["name"]) ?>"
                class="card-img-top"
                style="height:250px; object-fit:cover;">

        </div>

        <div class="card-body d-flex flex-column">

            <small class="text-muted">

                <?= htmlspecialchars($product["brand_name"]) ?>

            </small>

            <h5 class="mt-2 fw-bold">

                <?= htmlspecialchars($product["name"]) ?>

            </h5>

            <p class="text-muted mb-2">

                <?= htmlspecialchars($product["category_name"]) ?>

            </p>

            <p class="small text-secondary flex-grow-1">

                <?= htmlspecialchars(
                    substr($product["description"], 0, 90),
                ) ?>...

            </p>

            <?php if ($product["price"] > 0): ?>

            <h5 class="text-warning fw-bold">

                Rs. <?= number_format($product["price"], 2) ?>

            </h5>

            <?php endif; ?>

            <div class="d-grid gap-2 mt-3">

                <a
                    href="product-details.php?id=<?= $product["id"] ?>"
                    class="btn btn-dark">

                    View Details

                </a>

                <?php if ($product["stock"] > 0): ?>
                <form action="cart-action.php" method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product[
                        "id"
                    ] ?>">
                    <input type="hidden" name="return_to" value="products.php">
                    <button type="submit" class="btn btn-outline-dark btn-sm w-100"><i class="fas fa-cart-plus me-2"></i>Add to Cart</button>
                </form>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="col-12">

<div class="alert alert-warning text-center p-5">

<h3>

No Products Found

</h3>

<p>

Please try another search or filter.

</p>

<a href="products.php" class="btn btn-warning">

Show All Products

</a>

</div>

</div>

<?php endif; ?>

</div>

</div>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>
