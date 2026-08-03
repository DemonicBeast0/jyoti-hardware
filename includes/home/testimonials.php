<?php require_once __DIR__ . '/../../config/testimonials.php'; ?>
<section class="testimonials py-5">
    <div class="container">
        <div class="section-title text-center mb-5"><span>TESTIMONIALS</span><h2>What Our Customers Say</h2><p>Trusted by contractors, engineers and homeowners across Nepal.</p></div>
        <div class="swiper testimonialSwiper"><div class="swiper-wrapper">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="swiper-slide"><article class="testimonial-card"><img src="<?= htmlspecialchars($testimonial['image']); ?>" alt="<?= htmlspecialchars($testimonial['name']); ?>"><h5><?= htmlspecialchars($testimonial['name']); ?></h5><small><?= htmlspecialchars($testimonial['role']); ?></small><div class="stars" aria-label="5 out of 5 stars">&starf;&starf;&starf;&starf;&starf;</div><p><?= htmlspecialchars($testimonial['message']); ?></p></article></div>
            <?php endforeach; ?>
        </div><div class="swiper-pagination"></div></div>
    </div>
</section>
