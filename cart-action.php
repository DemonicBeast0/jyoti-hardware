<?php
session_start();
require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit();
}

$returnTo = $_POST["return_to"] ?? "cart.php";
if (!preg_match('/^[a-z0-9_-]+\.php(?:\?[a-z0-9=&_%.+-]+)?$/i', $returnTo)) {
    $returnTo = "cart.php";
}

$_SESSION["cart"] = $_SESSION["cart"] ?? [];
$action = $_POST["action"] ?? "";

if ($action === "add") {
    $id = filter_input(INPUT_POST, "product_id", FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare(
            "SELECT id, stock FROM products WHERE id = ? AND status = 1 LIMIT 1",
        );
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product && (int) $product["stock"] > 0) {
            $currentQuantity = (int) ($_SESSION["cart"][$id] ?? 0);
            $_SESSION["cart"][$id] = min(
                $currentQuantity + 1,
                (int) $product["stock"],
            );
        }
    }
} elseif (
    ($action === "update" || $action === "checkout") &&
    isset($_POST["quantity"]) &&
    is_array($_POST["quantity"])
) {
    $requestedQuantities = [];
    foreach ($_POST["quantity"] as $id => $quantity) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if (!$id || $quantity === false) {
            continue;
        }
        $requestedQuantities[$id] = max(0, $quantity);
    }

    $removeId = filter_input(INPUT_POST, "remove_product_id", FILTER_VALIDATE_INT);
    if ($removeId) {
        unset($_SESSION["cart"][$removeId], $requestedQuantities[$removeId]);
    }

    if ($requestedQuantities) {
        $placeholders = implode(",", array_fill(0, count($requestedQuantities), "?"));
        $stmt = $pdo->prepare(
            "SELECT id, stock FROM products WHERE status = 1 AND id IN ($placeholders)",
        );
        $stmt->execute(array_keys($requestedQuantities));
        $availableStock = [];
        foreach ($stmt->fetchAll() as $product) {
            $availableStock[(int) $product["id"]] = (int) $product["stock"];
        }

        foreach ($requestedQuantities as $id => $quantity) {
            $stock = $availableStock[$id] ?? 0;
            if ($quantity < 1 || $stock < 1) {
                unset($_SESSION["cart"][$id]);
                continue;
            }
            $_SESSION["cart"][$id] = min($quantity, $stock);
        }
    }

    if ($action === "checkout") {
        header("Location: quote.php");
        exit();
    }
} elseif ($action === "remove") {
    $id = filter_input(INPUT_POST, "product_id", FILTER_VALIDATE_INT);
    if ($id) {
        unset($_SESSION["cart"][$id]);
    }
} elseif ($action === "clear") {
    $_SESSION["cart"] = [];
}

if (($_SERVER["HTTP_X_REQUESTED_WITH"] ?? "") === "XMLHttpRequest") {
    header("Content-Type: application/json");
    echo json_encode(["cart_count" => array_sum($_SESSION["cart"])]);
    exit();
}

header("Location: " . $returnTo);
exit();
