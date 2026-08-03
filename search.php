<?php
$search = trim($_GET['q'] ?? $_GET['search'] ?? '');
if ($search !== '') {
    header('Location: products.php?search=' . rawurlencode($search));
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>
<section class="page-header"><div class="container"><h1>Search Products</h1><p>Find the hardware and supplies you need.</p></div></section>
<section class="py-5"><div class="container"><form action="products.php" method="get" class="mx-auto" style="max-width:680px"><label class="form-label fw-semibold" for="siteSearch">Search our products</label><div class="input-group"><input id="siteSearch" name="search" type="search" class="form-control" placeholder="e.g. drill, pipe, wire" required><button class="btn btn-warning" type="submit">Search</button></div></form></div></section>
<?php include 'includes/footer.php'; ?>
