<?php
require "auth.php";
require "../config/database.php";
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$notice = $_GET["notice"] ?? "";
$error = "";
$editing = [
    "id" => "",
    "name" => "",
    "logo" => "",
    "description" => "",
    "status" => 1,
];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? "")) {
        $error = "Your session has expired. Please try again.";
    } else {
        $action = $_POST["action"] ?? "";
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?: 0;
        if ($action === "save") {
            $name = trim($_POST["name"] ?? "");
            $logo = trim($_POST["logo"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $status = isset($_POST["status"]) ? 1 : 0;
            $editing = compact("id", "name", "logo", "description", "status");
            if ($name === "") {
                $error = "Brand name is required.";
            } else {
                $check = $pdo->prepare(
                    "SELECT id FROM brands WHERE name = ? AND id != ? LIMIT 1",
                );
                $check->execute([$name, $id]);
                if ($check->fetch()) {
                    $error = "A brand with this name already exists.";
                } elseif ($id) {
                    $pdo->prepare(
                        "UPDATE brands SET name = ?, logo = ?, description = ?, status = ? WHERE id = ?",
                    )->execute([
                        $name,
                        $logo ?: null,
                        $description ?: null,
                        $status,
                        $id,
                    ]);
                    header("Location: brands.php?notice=Brand updated.");
                    exit();
                } else {
                    $pdo->prepare(
                        "INSERT INTO brands (name, logo, description, status) VALUES (?, ?, ?, ?)",
                    )->execute([
                        $name,
                        $logo ?: null,
                        $description ?: null,
                        $status,
                    ]);
                    header("Location: brands.php?notice=Brand added.");
                    exit();
                }
            }
        } elseif ($action === "delete" && $id) {
            $count = $pdo->prepare(
                "SELECT COUNT(*) FROM products WHERE brand_id = ?",
            );
            $count->execute([$id]);
            if ((int) $count->fetchColumn() > 0) {
                $error =
                    "This brand is assigned to products and cannot be deleted. Mark it inactive instead.";
            } else {
                $pdo->prepare("DELETE FROM brands WHERE id = ?")->execute([
                    $id,
                ]);
                header("Location: brands.php?notice=Brand deleted.");
                exit();
            }
        }
    }
}
if (
    !$error &&
    isset($_GET["edit"]) &&
    ($id = filter_input(INPUT_GET, "edit", FILTER_VALIDATE_INT))
) {
    $statement = $pdo->prepare(
        "SELECT id, name, logo, description, status FROM brands WHERE id = ?",
    );
    $statement->execute([$id]);
    $editing = $statement->fetch() ?: $editing;
}
$brands = $pdo
    ->query(
        "SELECT b.*, COUNT(p.id) AS product_count FROM brands b LEFT JOIN products p ON p.brand_id = b.id GROUP BY b.id ORDER BY b.name",
    )
    ->fetchAll();
include "includes/header.php";
include "includes/sidebar.php";
include "includes/navbar.php";
?>
<div class="content"><div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><h2>Brands</h2><a href="brands.php" class="btn btn-outline-secondary">New Brand</a></div>
<?php if ($notice): ?><div class="alert alert-success"><?= htmlspecialchars(
    $notice,
) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars(
    $error,
) ?></div><?php endif; ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><h5><?= $editing[
    "id"
]
    ? "Edit Brand"
    : "Add Brand" ?></h5><form method="post"><input type="hidden" name="csrf_token" value="<?= $_SESSION[
    "csrf_token"
] ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $editing[
    "id"
] ?>"><div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= htmlspecialchars(
    $editing["name"],
) ?>" required></div><div class="mb-3"><label class="form-label">Logo path or URL</label><input class="form-control" name="logo" value="<?= htmlspecialchars(
    $editing["logo"] ?? "",
) ?>" placeholder="assets/images/brands/brand-logo.png"></div><div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4"><?= htmlspecialchars(
    $editing["description"] ?? "",
) ?></textarea></div><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" id="brandStatus" <?= $editing[
    "status"
]
    ? "checked"
    : "" ?>><label class="form-check-label" for="brandStatus">Active</label></div><button class="btn btn-warning">Save Brand</button></form></div></div></div><div class="col-lg-8"><div class="card shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead class="table-dark"><tr><th>Brand</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach (
    $brands
    as $brand
): ?><tr><td><strong><?= htmlspecialchars($brand["name"]) ?></strong><?php if (
    $brand["description"]
): ?><div class="small text-muted"><?= htmlspecialchars(
    $brand["description"],
) ?></div><?php endif; ?></td><td><?= $brand[
    "product_count"
] ?></td><td><span class="badge bg-<?= $brand["status"]
    ? "success"
    : "secondary" ?>"><?= $brand["status"]
    ? "Active"
    : "Inactive" ?></span></td><td><a class="btn btn-sm btn-primary" href="brands.php?edit=<?= $brand[
    "id"
] ?>"><i class="fas fa-edit"></i></a><form class="d-inline" method="post" onsubmit="return confirm('Delete this brand?');"><input type="hidden" name="csrf_token" value="<?= $_SESSION[
    "csrf_token"
] ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $brand[
    "id"
] ?>"><button class="btn btn-sm btn-danger" <?= $brand["product_count"]
    ? 'disabled title="Remove or reassign its products first"'
    : "" ?>><i class="fas fa-trash"></i></button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div></div></div></div>
<?php include "includes/footer.php"; ?>
