<?php
require '../auth.php';
require '../../config/database.php';

function slugify($text)
{
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
    $text = preg_replace('~[^-\\w]+~u', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'product';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = $_POST['id'] ?? '';
$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$category_id = trim($_POST['category_id'] ?? '');
$brand_id = trim($_POST['brand_id'] ?? '');
$badge = trim($_POST['badge'] ?? '');
$status = isset($_POST['status']) ? (int) $_POST['status'] : 0;
$stock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
$description = trim($_POST['description'] ?? '');

if (!is_numeric($id) || $name === '' || $price === '' || $category_id === '' || $brand_id === '') {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Please fill all required fields.'));
    exit;
}

if (!is_numeric($price) || $price < 0) {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Invalid price value.'));
    exit;
}

$stmt = $pdo->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$imagePath = $product['image'];

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageDir = __DIR__ . '/../../uploads/products';
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0755, true);
    }

    $originalName = basename($_FILES['image']['name']);
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filename = uniqid('prod_', true) . ($extension ? '.' . $extension : '');
    $destination = $imageDir . DIRECTORY_SEPARATOR . $filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
        if (!empty($imagePath) && file_exists(__DIR__ . '/../../' . $imagePath)) {
            @unlink(__DIR__ . '/../../' . $imagePath);
        }
        $imagePath = 'uploads/products/' . $filename;
    }
}

$slug = slugify($name);

$stmt = $pdo->prepare(
    'UPDATE products SET category_id = ?, brand_id = ?, name = ?, slug = ?, description = ?, image = ?, price = ?, stock = ?, badge = ?, status = ? WHERE id = ?'
);
$stmt->execute([
    $category_id,
    $brand_id,
    $name,
    $slug,
    $description,
    $imagePath,
    $price,
    $stock,
    $badge,
    $status,
    $id
]);

header('Location: index.php?updated=1');
exit;
