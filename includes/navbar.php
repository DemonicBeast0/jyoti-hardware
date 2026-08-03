<?php
require_once __DIR__ . '/../config/database.php';

$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$navSearch = trim($_GET['search'] ?? '');
$cartCount = array_sum($_SESSION['cart'] ?? []);
$selectedCategory = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT) ?: 0;
$navCategories = $pdo->query('SELECT id, name FROM categories WHERE status = 1 ORDER BY name ASC')->fetchAll();
?>
<nav class="navbar navbar-expand-xxl fixed-top" aria-label="Primary navigation">
    <div class="container">
        <a class="navbar-brand" href="index.php" aria-label="Jyoti Suppliers home">
            <img src="assets/images/logo/jyoti-suppliers.svg" alt="Jyoti Suppliers">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu"
            aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= in_array($currentPage, ['products.php', 'product-details.php'], true) && !($currentPage === 'products.php' && $selectedCategory) ? 'active' : ''; ?>" href="products.php">Products</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $currentPage === 'products.php' && $selectedCategory ? 'active' : ''; ?>" href="products.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                    <ul class="dropdown-menu shadow-lg border-0">
                        <li><a class="dropdown-item <?= $currentPage === 'products.php' && !$selectedCategory ? 'active' : ''; ?>" href="products.php"><i class="fas fa-border-all me-2" aria-hidden="true"></i>All Categories</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($navCategories as $category): ?>
                            <li><a class="dropdown-item <?= $selectedCategory === (int) $category['id'] ? 'active' : ''; ?>" href="products.php?category=<?= $category['id']; ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'brands.php' ? 'active' : ''; ?>" href="brands.php">Brands</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'about.php' ? 'active' : ''; ?>" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions d-flex align-items-center">
                <div class="navbar-search-wrap">
                    <form class="navbar-search" action="products.php" method="get" role="search">
                        <label class="visually-hidden" for="navbarSearch">Search products</label>
                        <input id="navbarSearch" name="search" type="search" value="<?= htmlspecialchars($navSearch); ?>" placeholder="Search products" autocomplete="off" aria-autocomplete="list" aria-controls="searchSuggestions" aria-expanded="false">
                        <button type="submit" aria-label="Search products"><i class="fas fa-search" aria-hidden="true"></i></button>
                    </form>
                    <div id="searchSuggestions" class="search-suggestions" role="listbox" hidden></div>
                </div>
                <a href="cart.php" class="cart-link <?= $currentPage === 'cart.php' ? 'active' : ''; ?>" aria-label="View cart">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i><span>Cart<?php if ($cartCount > 0): ?> (<?= $cartCount; ?>)<?php endif; ?></span>
                </a>
                <a href="contact.php" class="nav-inquiry d-none d-xxl-inline-flex"><i class="fas fa-paper-plane" aria-hidden="true"></i> Inquiry</a>
            </div>
        </div>
    </div>
</nav>
