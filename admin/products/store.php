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
    header('Location: create.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$category_id = trim($_POST['category_id'] ?? '');
$brand_id = trim($_POST['brand_id'] ?? '');
$status = isset($_POST['status']) ? (int) $_POST['status'] : 0;
$description = trim($_POST['description'] ?? '');

if ($name === '' || $price === '' || $category_id === '' || $brand_id === '' || !isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    header('Location: create.php?error=' . urlencode('Please fill all required fields and upload an image.'));
    exit;
}

if (!is_numeric($price) || $price < 0) {
    header('Location: create.php?error=' . urlencode('Invalid price value.'));
    exit;
}

$imageDir = __DIR__ . '/../../uploads/products';
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

$originalName = basename($_FILES['image']['name']);
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$filename = uniqid('prod_', true) . ($extension ? '.' . $extension : '');
$destination = $imageDir . DIRECTORY_SEPARATOR . $filename;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
    header('Location: create.php?error=' . urlencode('Unable to upload image.'));
    exit;
}

$imagePath = 'uploads/products/' . $filename;
$slug = slugify($name);

$stmt = $pdo->prepare(
    'INSERT INTO products (category_id, brand_id, name, slug, description, image, price, stock, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $category_id,
    $brand_id,
    $name,
    $slug,
    $description,
    $imagePath,
    $price,
    0,
    $status
]);

$productId = (int) $pdo->lastInsertId();

if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['error'])) {
    $additionalImageStmt = $pdo->prepare(
        'INSERT INTO product_images (product_id, image) VALUES (?, ?)'
    );

    foreach ($_FILES['additional_images']['error'] as $index => $uploadError) {
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            continue;
        }

        $originalName = basename($_FILES['additional_images']['name'][$index]);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . ($extension ? '.' . $extension : '');
        $destination = $imageDir . DIRECTORY_SEPARATOR . $filename;

        if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$index], $destination)) {
            $additionalImageStmt->execute([
                $productId,
                'uploads/products/' . $filename
            ]);
        }
    }
}

header('Location: index.php?success=1');
exit;
