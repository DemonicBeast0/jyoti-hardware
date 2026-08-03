<?php
require_once 'config/brands.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Our Brands</h1>
        <p>Trusted brands for dependable hardware and construction solutions.</p>
    </div>
</section>

<section class="brands-page py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <span>AUTHORIZED BRANDS</span>
            <h2>Brands We Deal With</h2>
            <p>We supply genuine products from reliable local and international manufacturers.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($brands as $brand): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <article class="brand-card h-100">
                        <div class="brand-logo-wrap">
                            <img src="<?= htmlspecialchars($brand['logo']); ?>"
                                 alt="<?= htmlspecialchars($brand['name']); ?> logo">
                        </div>
                        <div class="brand-card-body">
                            <h2><?= htmlspecialchars($brand['name']); ?></h2>
                            <p><?= htmlspecialchars($brand['description']); ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
