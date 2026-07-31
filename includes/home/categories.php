<?php
require_once __DIR__ . '/../../config/categories.php';
?>

<!-- =========================================
     PRODUCT CATEGORIES
========================================= -->

<section class="categories py-5">

    <div class="container">

        <div class="section-title text-center mb-5">

            <span>OUR CATEGORIES</span>

            <h2>Shop By Category</h2>

            <p>
                Find the right hardware products for construction,
                electrical, plumbing and industrial projects.
            </p>

        </div>

        <div class="row g-4">

            <?php foreach($categories as $category): ?>

            <div class="col-lg-3 col-md-6">

                <div class="category-card">

                    <div class="category-image">

                        <img src="<?= $category['image']; ?>"
                             alt="<?= htmlspecialchars($category['title']); ?>">

                    </div>

                    <div class="category-content">

                        <i class="<?= $category['icon']; ?>"></i>

                        <h4><?= htmlspecialchars($category['title']); ?></h4>

                        <a href="products.php?category=<?= urlencode($category['slug']); ?>"
                           class="category-btn">

                            Explore Products

                            <i class="fas fa-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>