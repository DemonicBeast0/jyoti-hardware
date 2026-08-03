<footer class="footer">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-4">
                <h3>Jyoti Suppliers</h3>
                <p>Your trusted destination for premium hardware, construction materials, electrical supplies, plumbing products and industrial tools in Nepal.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="col-lg-2"><h5>Quick Links</h5><ul><li><a href="index.php">Home</a></li><li><a href="about.php">About</a></li><li><a href="products.php">Products</a></li><li><a href="brands.php">Brands</a></li><li><a href="contact.php">Contact</a></li></ul></div>
            <div class="col-lg-3"><h5>Categories</h5><ul><li><a href="products.php?category=1">Power Tools</a></li><li><a href="products.php?category=2">Hand Tools</a></li><li><a href="products.php?category=3">Electrical</a></li><li><a href="products.php?category=4">Plumbing</a></li><li><a href="products.php?category=5">Paints</a></li></ul></div>
            <div class="col-lg-3"><h5>Contact</h5><p><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Damak, Nepal</p><p><a href="tel:+9779800000000"><i class="fas fa-phone" aria-hidden="true"></i> +977 9800000000</a></p><p><a href="mailto:info@jyotihardware.com"><i class="fas fa-envelope" aria-hidden="true"></i> info@jyotihardware.com</a></p></div>
        </div>
        <hr>
        <div class="copyright">&copy; <?= date('Y'); ?> Jyoti Suppliers | Designed &amp; Developed by <strong>Pratik Majhi</strong></div>
    </div>
</footer>
<a href="https://wa.me/9779800000000" class="whatsapp" target="_blank" rel="noopener" aria-label="Chat with Jyoti Suppliers on WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
<button id="topBtn" type="button" aria-label="Back to top"><i class="fas fa-arrow-up" aria-hidden="true"></i></button>
<?php if (basename($_SERVER['PHP_SELF'] ?? '') !== 'products.php'): ?>
    <?php include 'modal.php'; ?>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/../assets/js/main.js'); ?>"></script>
</body>
</html>
