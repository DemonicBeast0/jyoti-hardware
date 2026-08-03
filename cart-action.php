<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$returnTo = $_POST['return_to'] ?? 'cart.php';
if (!preg_match('/^[a-z0-9_-]+\.php(?:\?[a-z0-9=&_%.-]+)?$/i', $returnTo)) {
    $returnTo = 'cart.php';
}

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('SELECT id, stock FROM products WHERE id = ? AND status = 1 LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product && (int) $product['stock'] > 0) {
            $currentQuantity = (int) ($_SESSION['cart'][$id] ?? 0);
            $_SESSION['cart'][$id] = min($currentQuantity + 1, (int) $product['stock']);
        }
    }
} elseif ($action === 'update' && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if (!$id) {
            continue;
        }
        if (!$quantity || $quantity < 1) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = min($quantity, 99);
        }
    }
} elseif ($action === 'remove') {
    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if ($id) {
        unset($_SESSION['cart'][$id]);
    }
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
}

header('Location: ' . $returnTo);
exit;
