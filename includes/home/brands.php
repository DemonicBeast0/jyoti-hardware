<?php
require_once __DIR__ . '/../../config/brands.php';
?>

<section class="py-5 bg-white">

    <div class="container">

        <div class="section-title text-center mb-5">

            <span>AUTHORIZED BRANDS</span>

            <h2>Brands We Deal With</h2>

        </div>

        <div class="row justify-content-center g-4">

            <?php foreach($brands as $brand): ?>

            <div class="col-lg-2 col-md-3 col-6 text-center">

                <div class="brand-box">

                    <img src="<?= $brand['logo']; ?>"
                         alt="<?= htmlspecialchars($brand['name']); ?>"
                         class="img-fluid">

                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>