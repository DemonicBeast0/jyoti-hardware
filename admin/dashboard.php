<?php

require 'auth.php';
require '../config/database.php';

/* ===============================
   Dashboard Statistics
=============================== */

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

$totalBrands = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();

$totalQuotes = $pdo->query("SELECT COUNT(*) FROM quotes")->fetchColumn();

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

?>

<div class="content">

    <div class="container-fluid">

        <div class="row g-4">

            <div class="col-lg-3 col-md-6">

                <a class="dashboard-card text-decoration-none d-block text-reset" href="products/index.php">

                    <i class="fas fa-box"></i>

                    <h2><?= $totalProducts; ?></h2>

                    <p>Total Products</p>

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a class="dashboard-card text-decoration-none d-block text-reset" href="categories.php">

                    <i class="fas fa-layer-group"></i>

                    <h2><?= $totalCategories; ?></h2>

                    <p>Total Categories</p>

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a class="dashboard-card text-decoration-none d-block text-reset" href="brands.php">

                    <i class="fas fa-award"></i>

                    <h2><?= $totalBrands; ?></h2>

                    <p>Total Brands</p>

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a class="dashboard-card text-decoration-none d-block text-reset" href="quotes.php">

                    <i class="fas fa-file-alt"></i>

                    <h2><?= $totalQuotes; ?></h2>

                    <p>Total Quote Requests</p>

                </a>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
