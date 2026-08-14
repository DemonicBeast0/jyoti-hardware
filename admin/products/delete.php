<?php
require '../auth.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product && !empty($product['image'])) {
    $imagePath = __DIR__ . '/../../' . $product['image'];
    if (file_exists($imagePath)) {
        @unlink($imagePath);
    }
}

$imageStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id = ?');
$imageStmt->execute([$id]);
foreach ($imageStmt->fetchAll() as $additionalImage) {
    $imagePath = __DIR__ . '/../../' . $additionalImage['image'];
    if (file_exists($imagePath)) {
        @unlink($imagePath);
    }
}

$pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);

header('Location: index.php?deleted=1');
exit;
