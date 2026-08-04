<?php
require_once "config/database.php";

header("Content-Type: application/json; charset=utf-8");

$query = trim($_GET["q"] ?? "");
if (mb_strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

$stmt = $pdo->prepare(
    'SELECT products.id, products.name, products.image, brands.name AS brand_name
     FROM products
     LEFT JOIN brands ON products.brand_id = brands.id
     WHERE products.status = 1 AND products.name LIKE ?
     ORDER BY products.name ASC
     LIMIT 6',
);
$stmt->execute(["%" . $query . "%"]);
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_SLASHES);
