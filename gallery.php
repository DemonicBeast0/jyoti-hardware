<?php include "includes/header.php";
include "includes/navbar.php";
?>
<section class="page-header"><div class="container"><h1>Gallery</h1><p>A look at our store, products, and trusted hardware range.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4">
<?php
$galleryImages = [
    ["assets/images/about/store.jpg", "Jyoti Suppliers store"],
    ["assets/images/powertools/power-drill.jpg", "Power tools"],
    ["assets/images/handtools/hammer.jpg", "Hand tools"],
    ["assets/images/electric/MCB.jpg", "Electrical supplies"],
    ["assets/images/plumbing/pipe-fittings.jpg", "Plumbing fittings"],
    ["assets/images/paints/exterior-paint.jpg", "Paint products"],
];
foreach ($galleryImages as [$image, $alt]): ?>
<div class="col-md-6 col-lg-4"><figure class="card border-0 shadow-sm h-100 overflow-hidden mb-0"><img src="<?= htmlspecialchars(
    $image,
) ?>" alt="<?= htmlspecialchars(
    $alt,
) ?>" class="w-100" style="height:260px;object-fit:cover"><figcaption class="p-3 fw-semibold"><?= htmlspecialchars(
    $alt,
) ?></figcaption></figure></div>
<?php endforeach;
?>
</div></div></section>
<?php include "includes/footer.php"; ?>
