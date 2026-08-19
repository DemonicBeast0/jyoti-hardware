<?php
require_once "config/database.php";

$branches = $pdo
    ->query(
        "SELECT name, address, map_location, phone, email FROM branches WHERE status = 1 ORDER BY name ASC",
    )
    ->fetchAll();

include "includes/header.php";
include "includes/navbar.php";
?>
<section class="page-header">
    <div class="container">
        <h1>Our Branches</h1>
        <p>Visit the Jyoti Suppliers branch that is most convenient for you.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (!$branches): ?>
            <div class="alert alert-info mb-0">Branch details will be available soon.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($branches as $branch): ?>
                    <div class="col-md-6">
                        <article class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 mb-3"><i class="fas fa-store text-warning me-2" aria-hidden="true"></i><?= htmlspecialchars(
                                    $branch["name"],
                                    ENT_QUOTES,
                                    "UTF-8",
                                ) ?></h2>
                                <p><i class="fas fa-map-marker-alt text-warning me-2" aria-hidden="true"></i><?= htmlspecialchars(
                                    $branch["address"],
                                    ENT_QUOTES,
                                    "UTF-8",
                                ) ?></p>
                                <?php if ($branch["phone"]): ?>
                                    <p><a class="text-dark" href="tel:<?= htmlspecialchars(
                                        preg_replace('/[^+0-9]/', '', $branch["phone"]),
                                        ENT_QUOTES,
                                        "UTF-8",
                                    ) ?>"><i class="fas fa-phone text-warning me-2" aria-hidden="true"></i><?= htmlspecialchars(
    $branch["phone"],
    ENT_QUOTES,
    "UTF-8",
) ?></a></p>
                                <?php endif; ?>
                                <?php if ($branch["email"]): ?>
                                    <p><a class="text-dark" href="mailto:<?= htmlspecialchars(
                                        $branch["email"],
                                        ENT_QUOTES,
                                        "UTF-8",
                                    ) ?>"><i class="fas fa-envelope text-warning me-2" aria-hidden="true"></i><?= htmlspecialchars(
    $branch["email"],
    ENT_QUOTES,
    "UTF-8",
) ?></a></p>
                                <?php endif; ?>
                                <div class="ratio ratio-4x3 mt-3">
                                    <iframe src="https://www.google.com/maps?q=<?= rawurlencode(
                                        $branch["map_location"],
                                    ) ?>&amp;output=embed" title="Map for <?= htmlspecialchars(
    $branch["name"],
    ENT_QUOTES,
    "UTF-8",
) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                                <a class="btn btn-outline-dark btn-sm mt-3" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode(
                                    $branch["map_location"],
                                ) ?>" target="_blank" rel="noopener"><i class="fas fa-directions me-1" aria-hidden="true"></i>Get Directions</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php include "includes/footer.php"; ?>
