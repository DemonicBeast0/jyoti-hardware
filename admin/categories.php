<?php
require "auth.php";
require "../config/database.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function categorySlug(string $value): string
{
    $slug = strtolower(trim(preg_replace("/[^a-z0-9]+/i", "-", $value), "-"));
    return $slug ?: "category";
}

$notice = $_GET["notice"] ?? "";
$error = "";
$editing = ["id" => "", "name" => "", "slug" => "", "status" => 1];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"] ?? "")) {
        $error = "Your session has expired. Please try again.";
    } else {
        $action = $_POST["action"] ?? "";
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?: 0;
        if ($action === "save") {
            $name = trim($_POST["name"] ?? "");
            $slug = categorySlug(trim($_POST["slug"] ?? "") ?: $name);
            $status = isset($_POST["status"]) ? 1 : 0;
            $editing = compact("id", "name", "slug", "status");
            if ($name === "") {
                $error = "Category name is required.";
            } else {
                $check = $pdo->prepare(
                    "SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1",
                );
                $check->execute([$slug, $id]);
                if ($check->fetch()) {
                    $error = "That category slug is already in use.";
                } elseif ($id) {
                    $pdo->prepare(
                        "UPDATE categories SET name = ?, slug = ?, status = ? WHERE id = ?",
                    )->execute([$name, $slug, $status, $id]);
                    header("Location: categories.php?notice=Category updated.");
                    exit();
                } else {
                    $pdo->prepare(
                        "INSERT INTO categories (name, slug, status) VALUES (?, ?, ?)",
                    )->execute([$name, $slug, $status]);
                    header("Location: categories.php?notice=Category added.");
                    exit();
                }
            }
        } elseif ($action === "delete" && $id) {
            $count = $pdo->prepare(
                "SELECT COUNT(*) FROM products WHERE category_id = ?",
            );
            $count->execute([$id]);
            if ((int) $count->fetchColumn() > 0) {
                $error =
                    "This category is assigned to products and cannot be deleted. Mark it inactive instead.";
            } else {
                $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([
                    $id,
                ]);
                header("Location: categories.php?notice=Category deleted.");
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
        "SELECT id, name, slug, status FROM categories WHERE id = ?",
    );
    $statement->execute([$id]);
    $editing = $statement->fetch() ?: $editing;
}
$categories = $pdo
    ->query(
        "SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name",
    )
    ->fetchAll();
include "includes/header.php";
include "includes/sidebar.php";
include "includes/navbar.php";
?>
<div class="content"><div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><h2>Categories</h2><a href="categories.php" class="btn btn-outline-secondary">New Category</a></div>
<?php if ($notice): ?><div class="alert alert-success"><?= htmlspecialchars(
    $notice,
) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars(
    $error,
) ?></div><?php endif; ?>
<div class="row g-4"><div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><h5><?= $editing[
    "id"
]
    ? "Edit Category"
    : "Add Category" ?></h5><form method="post"><input type="hidden" name="csrf_token" value="<?= $_SESSION[
    "csrf_token"
] ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $editing[
    "id"
] ?>"><div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= htmlspecialchars(
    $editing["name"],
) ?>" required></div><div class="mb-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= htmlspecialchars(
    $editing["slug"],
) ?>" placeholder="Generated from name if left blank"></div><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="status" id="categoryStatus" <?= $editing[
    "status"
]
    ? "checked"
    : "" ?>><label class="form-check-label" for="categoryStatus">Active</label></div><button class="btn btn-warning">Save Category</button></form></div></div></div><div class="col-lg-8"><div class="card shadow-sm"><div class="card-body table-responsive"><table class="table align-middle"><thead class="table-dark"><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach (
    $categories
    as $category
): ?><tr><td><?= htmlspecialchars(
    $category["name"],
) ?></td><td><code><?= htmlspecialchars(
    $category["slug"],
) ?></code></td><td><?= $category[
    "product_count"
] ?></td><td><span class="badge bg-<?= $category["status"]
    ? "success"
    : "secondary" ?>"><?= $category["status"]
    ? "Active"
    : "Inactive" ?></span></td><td><a class="btn btn-sm btn-primary" href="categories.php?edit=<?= $category[
    "id"
] ?>"><i class="fas fa-edit"></i></a><form class="d-inline" method="post" onsubmit="return confirm('Delete this category?');"><input type="hidden" name="csrf_token" value="<?= $_SESSION[
    "csrf_token"
] ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $category[
    "id"
] ?>"><button class="btn btn-sm btn-danger" <?= $category["product_count"]
    ? 'disabled title="Remove or reassign its products first"'
    : "" ?>><i class="fas fa-trash"></i></button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div></div></div></div>
<?php include "includes/footer.php"; ?>
