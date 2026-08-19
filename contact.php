<?php
require_once "config/database.php";
require_once "includes/validation.php";

$products = $pdo
    ->query(
        "SELECT id, name, brand_id FROM products WHERE status = 1 ORDER BY name ASC",
    )
    ->fetchAll();
$branches = $pdo
    ->query(
        "SELECT name, address, map_location, phone, email FROM branches WHERE status = 1 ORDER BY name ASC",
    )
    ->fetchAll();
$productIds = array_map("intval", array_column($products, "id"));
$selectedProductId =
    filter_input(INPUT_GET, "product_id", FILTER_VALIDATE_INT) ?: 0;
$form = [
    "product_id" => $selectedProductId,
    "customer_name" => "",
    "email" => "",
    "phone" => "",
    "company" => "",
    "quantity" => 1,
    "message" => "",
];
$error = "";
$success = isset($_GET["success"]);

if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "POST") {
    $form = [
        "product_id" =>
            filter_input(INPUT_POST, "product_id", FILTER_VALIDATE_INT) ?: 0,
        "customer_name" => trim($_POST["customer_name"] ?? ""),
        "email" => trim($_POST["email"] ?? ""),
        "phone" => trim($_POST["phone"] ?? ""),
        "company" => trim($_POST["company"] ?? ""),
        "quantity" => max(1, (int) ($_POST["quantity"] ?? 1)),
        "message" => trim($_POST["message"] ?? ""),
    ];

    if (!in_array($form["product_id"], $productIds, true)) {
        $error = "Please select a product.";
    } elseif ($form["customer_name"] === "" || $form["phone"] === "") {
        $error = "Please enter your name and phone number.";
    } elseif (($phone = normalizeNepalPhoneNumber($form["phone"])) === null) {
        $error = "Please enter a valid Nepal mobile number.";
    } elseif (
        $form["email"] !== "" &&
        !filter_var($form["email"], FILTER_VALIDATE_EMAIL)
    ) {
        $error = "Please enter a valid email address.";
    } else {
        $form["phone"] = $phone;
        $productName =
            $products[array_search($form["product_id"], $productIds, true)][
                "name"
            ];
        $statement = $pdo->prepare(
            "INSERT INTO quotes (product_id, product_name, customer_name, email, phone, company, quantity, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        );
        $statement->execute([
            $form["product_id"],
            $productName,
            $form["customer_name"],
            $form["email"],
            $form["phone"],
            $form["company"],
            $form["quantity"],
            $form["message"],
        ]);
        header("Location: contact.php?success=1");
        exit();
    }
}

include "includes/header.php";
include "includes/navbar.php";
?>
<section class="page-header"><div class="container"><h1>Contact Us</h1><p>Ask about products, availability, and bulk orders.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4"><div class="col-lg-5"><h2>Let's Talk</h2><p class="text-muted">Contact Jyoti Suppliers for product guidance, project supply requirements, or an order inquiry.</p><?php if ($branches): ?><?php foreach ($branches as $branch): ?><div class="card border-0 shadow-sm p-4 mb-3"><h3 class="h5 mb-3"><?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?></h3><p class="mb-3"><i class="fas fa-map-marker-alt text-warning me-2"></i><?= htmlspecialchars($branch["address"], ENT_QUOTES, "UTF-8") ?></p><?php if ($branch["phone"]): ?><p class="mb-3"><a class="text-dark" href="tel:<?= htmlspecialchars(preg_replace('/[^+0-9]/', '', $branch["phone"]), ENT_QUOTES, "UTF-8") ?>"><i class="fas fa-phone text-warning me-2"></i><?= htmlspecialchars($branch["phone"], ENT_QUOTES, "UTF-8") ?></a></p><?php endif; ?><?php if ($branch["email"]): ?><p class="mb-0"><a class="text-dark" href="mailto:<?= htmlspecialchars($branch["email"], ENT_QUOTES, "UTF-8") ?>"><i class="fas fa-envelope text-warning me-2"></i><?= htmlspecialchars($branch["email"], ENT_QUOTES, "UTF-8") ?></a></p><?php endif; ?></div><?php endforeach; ?><?php else: ?><div class="card border-0 shadow-sm p-4"><p class="mb-0"><i class="fas fa-map-marker-alt text-warning me-2"></i>Itahari, Sunsari, Nepal</p></div><?php endif; ?></div><div class="col-lg-7"><div class="card border-0 shadow-sm p-4 p-md-5"><h2>Send an Inquiry</h2><p class="text-muted">Tell us what you need and we'll get back to you with availability and pricing.</p>
<?php if (
    $success
): ?><div class="alert alert-success" role="alert"><strong>Thank you!</strong> Your inquiry has been sent. We'll be in touch shortly.</div><?php endif; ?>
<?php if (
    $error
): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars(
    $error,
    ENT_QUOTES,
    "UTF-8",
) ?></div><?php endif; ?>
<form method="post">
<div class="mb-3"><label class="form-label fw-semibold" for="product_id">Product <span class="text-danger">*</span></label><select class="form-select" id="product_id" name="product_id" required><option value="">Select a product</option><?php foreach (
    $products
    as $product
): ?><option value="<?= $product["id"] ?>"<?= $form["product_id"] ===
(int) $product["id"]
    ? " selected"
    : "" ?>><?= htmlspecialchars(
    $product["name"],
    ENT_QUOTES,
    "UTF-8",
) ?></option><?php endforeach; ?></select></div>
<div class="row"><div class="col-md-6"><div class="mb-3"><label class="form-label fw-semibold" for="customer_name">Full Name <span class="text-danger">*</span></label><input class="form-control" id="customer_name" name="customer_name" value="<?= htmlspecialchars(
    $form["customer_name"],
    ENT_QUOTES,
    "UTF-8",
) ?>" required></div></div><div class="col-md-6"><div class="mb-3"><label class="form-label fw-semibold" for="phone">Phone Number <span class="text-danger">*</span></label><input class="form-control" id="phone" name="phone" value="<?= htmlspecialchars(
    $form["phone"],
    ENT_QUOTES,
    "UTF-8",
) ?>" type="tel" inputmode="tel" autocomplete="tel" pattern="(?:\+?977[\s-]?)?9(?:[\s-]?[0-9]){9}" title="Enter a Nepal mobile number, for example 9800000000 or +977 9800000000." maxlength="17" placeholder="+977 98XXXXXXXX" required></div></div></div>
<div class="row"><div class="col-md-6"><div class="mb-3"><label class="form-label fw-semibold" for="email">Email Address</label><input class="form-control" id="email" type="email" name="email" value="<?= htmlspecialchars(
    $form["email"],
    ENT_QUOTES,
    "UTF-8",
) ?>"></div></div><div class="col-md-6"><div class="mb-3"><label class="form-label fw-semibold" for="company">Company Name</label><input class="form-control" id="company" name="company" value="<?= htmlspecialchars(
    $form["company"],
    ENT_QUOTES,
    "UTF-8",
) ?>"></div></div></div>
<div class="mb-3"><label class="form-label fw-semibold" for="quantity">Required Quantity</label><input class="form-control" id="quantity" type="number" name="quantity" value="<?= $form[
    "quantity"
] ?>" min="1"></div>
<div class="mb-4"><label class="form-label fw-semibold" for="message">How can we help?</label><textarea class="form-control" id="message" name="message" rows="5" placeholder="Share your requirements, preferred size, or delivery details."><?= htmlspecialchars(
    $form["message"],
    ENT_QUOTES,
    "UTF-8",
) ?></textarea></div>
<button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-paper-plane me-2"></i>Send Inquiry</button>
</form></div></div></div></div></section>
<?php if ($branches): ?><section class="pb-5"><div class="container"><h2 class="mb-4">Find Our Branches</h2><div class="row g-4"><?php foreach ($branches as $branch): ?><div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><h3 class="h5"><?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?></h3><div class="ratio ratio-4x3"><iframe src="https://www.google.com/maps?q=<?= rawurlencode($branch["map_location"]) ?>&amp;output=embed" title="Map for <?= htmlspecialchars($branch["name"], ENT_QUOTES, "UTF-8") ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div><a class="btn btn-outline-dark btn-sm mt-3" href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($branch["map_location"]) ?>" target="_blank" rel="noopener">Open in Google Maps</a></div></div></div><?php endforeach; ?></div></div></section><?php endif; ?>
<?php include "includes/footer.php"; ?>
