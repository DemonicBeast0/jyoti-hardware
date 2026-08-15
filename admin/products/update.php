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
$status = isset($_POST['status']) ? (int) $_POST['status'] : 0;
$stock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
$description = trim($_POST['description'] ?? '');
$productUrl = trim($_POST['product_url'] ?? '');

if (!is_numeric($id) || $name === '' || $price === '' || $category_id === '' || $brand_id === '') {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Please fill all required fields.'));
    exit;
}

if (!is_numeric($price) || $price < 0) {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Invalid price value.'));
    exit;
}

if ($productUrl !== '' && filter_var($productUrl, FILTER_VALIDATE_URL) === false) {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Please enter a valid product URL.'));
    exit;
}

if ($productUrl !== '' && !in_array(strtolower((string) parse_url($productUrl, PHP_URL_SCHEME)), ['http', 'https'], true)) {
    header('Location: edit.php?id=' . urlencode($id) . '&error=' . urlencode('Product URL must start with http:// or https://.'));
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

if (!empty($_POST['remove_image_ids']) && is_array($_POST['remove_image_ids'])) {
    $removeImageStmt = $pdo->prepare(
        'SELECT id, image FROM product_images WHERE id = ? AND product_id = ? LIMIT 1'
    );
    $deleteImageStmt = $pdo->prepare('DELETE FROM product_images WHERE id = ? AND product_id = ?');

    foreach ($_POST['remove_image_ids'] as $imageId) {
        if (!ctype_digit((string) $imageId)) {
            continue;
        }

        $removeImageStmt->execute([(int) $imageId, (int) $id]);
        $additionalImage = $removeImageStmt->fetch();

        if ($additionalImage) {
            $filePath = __DIR__ . '/../../' . $additionalImage['image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $deleteImageStmt->execute([(int) $imageId, (int) $id]);
        }
    }
}

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
    'UPDATE products SET category_id = ?, brand_id = ?, name = ?, slug = ?, description = ?, image = ?, product_url = ?, price = ?, stock = ?, status = ? WHERE id = ?'
);
$stmt->execute([
    $category_id,
    $brand_id,
    $name,
    $slug,
    $description,
    $imagePath,
    $productUrl ?: null,
    $price,
    $stock,
    $status,
    $id
]);

if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['error'])) {
    $imageDir = __DIR__ . '/../../uploads/products';
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0755, true);
    }

    $additionalImageStmt = $pdo->prepare(
        'INSERT INTO product_images (product_id, image) VALUES (?, ?)'
    );

    foreach ($_FILES['additional_images']['error'] as $index => $uploadError) {
        if ($uploadError !== UPLOAD_ERR_OK) {
            continue;
        }

        $originalName = basename($_FILES['additional_images']['name'][$index]);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . ($extension ? '.' . $extension : '');
        $destination = $imageDir . DIRECTORY_SEPARATOR . $filename;

        if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$index], $destination)) {
            $additionalImageStmt->execute([$id, 'uploads/products/' . $filename]);
        }
    }
}

header('Location: index.php?updated=1');
exit;
