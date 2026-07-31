<?php
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'config/brands.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1>Authorized Dealers</h1>
        <p>Home / Authorized Dealers</p>
    </div>
</section>

<!-- Intro -->
<section class="py-5">
    <div class="container text-center">

        <span class="text-warning fw-bold">
            OFFICIAL PARTNERS
        </span>

        <h2 class="fw-bold mt-2">
            Genuine Products From Trusted Brands
        </h2>

        <p class="mt-3 text-muted">
            Jyoti Hardware & Suppliers proudly supplies genuine products from
            internationally recognized brands. Our partnerships ensure quality,
            warranty support, and reliable after-sales service.
        </p>

    </div>
</section>

<!-- Brands -->

<section class="pb-5">

<div class="container">

<div class="row g-4">

<?php foreach($brands as $brand): ?>

<div class="col-lg-3 col-md-4 col-sm-6">

<div class="dealer-card">

<img
src="<?= $brand['logo']; ?>"
alt="<?= $brand['name']; ?>">

<h5>

<?= $brand['name']; ?>

</h5>

<p>

<?= $brand['description']; ?>

</p>

<span class="badge bg-success">

Authorized Dealer

</span>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</section>

<!-- Why Choose -->

<section class="py-5 bg-light">

<div class="container">

<div class="row g-4">

<div class="col-md-4">

<div class="feature-box">

<i class="fas fa-certificate"></i>

<h4>100% Genuine</h4>

<p>

All products are supplied directly from authorized distributors.

</p>

</div>

</div>

<div class="col-md-4">

<div class="feature-box">

<i class="fas fa-shield-alt"></i>

<h4>Manufacturer Warranty</h4>

<p>

Enjoy genuine warranty support from official brands.

</p>

</div>

</div>

<div class="col-md-4">

<div class="feature-box">

<i class="fas fa-headset"></i>

<h4>After Sales Support</h4>

<p>

Professional guidance and technical assistance.

</p>

</div>

</div>

</div>

</div>

</section>

<?php include 'includes/footer.php'; ?>