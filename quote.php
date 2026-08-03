<?php
require_once 'config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$totalQuantity = 0;

if ($cart) {
    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $pdo->prepare("SELECT id, name, image, price, stock FROM products WHERE status = 1 AND id IN ($placeholders)");
        $statement->execute($ids);
        foreach ($statement->fetchAll() as $product) {
            $quantity = min((int) ($cart[$product['id']] ?? 0), max(1, (int) $product['stock']));
            if ($quantity > 0) {
                $product['quantity'] = $quantity;
                $items[] = $product;
                $totalQuantity += $quantity;
            }
        }
    }
}

if (!$items) {
    header('Location: cart.php');
    exit;
}

$form = ['customer_name' => '', 'email' => '', 'phone' => '', 'company' => '', 'message' => ''];
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $form = [
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'company' => trim($_POST['company'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];

    if ($form['customer_name'] === '' || $form['phone'] === '') {
        $error = 'Please enter your name and phone number.';
    } elseif ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $productSummary = implode(', ', array_map(static fn(array $item): string => $item['name'] . ' × ' . $item['quantity'], $items));
        $statement = $pdo->prepare('INSERT INTO quotes (product_id, product_name, customer_name, email, phone, company, quantity, message) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([$productSummary, $form['customer_name'], $form['email'], $form['phone'], $form['company'], $totalQuantity, $form['message']]);
        $_SESSION['cart'] = [];
        header('Location: cart.php?order_success=1');
        exit;
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>
<section class="page-header"><div class="container"><h1>Place Your Order</h1><p>Confirm your items and send your order request.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4"><div class="col-lg-5"><div class="card border-0 shadow-sm p-4"><h2 class="h4 mb-3">Your Order</h2><ul class="list-group list-group-flush mb-0"><?php foreach ($items as $item): ?><li class="list-group-item px-0 d-flex justify-content-between gap-3"><span><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?> <small class="text-muted">× <?= $item['quantity']; ?></small></span><strong>Rs. <?= number_format($item['price'] * $item['quantity'], 2); ?></strong></li><?php endforeach; ?></ul></div></div><div class="col-lg-7"><div class="card border-0 shadow-sm p-4 p-md-5"><h2>Send Quote Request</h2><p class="text-muted">Share your contact details and our team will confirm availability and final pricing.</p><?php if ($error): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?><form method="post"><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="customer_name">Full Name <span class="text-danger">*</span></label><input class="form-control" id="customer_name" name="customer_name" value="<?= htmlspecialchars($form['customer_name'], ENT_QUOTES, 'UTF-8'); ?>" required></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="phone">Phone Number <span class="text-danger">*</span></label><input class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($form['phone'], ENT_QUOTES, 'UTF-8'); ?>" required></div></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="email">Email Address</label><input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars($form['email'], ENT_QUOTES, 'UTF-8'); ?>"></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="company">Company Name</label><input class="form-control" id="company" name="company" value="<?= htmlspecialchars($form['company'], ENT_QUOTES, 'UTF-8'); ?>"></div></div><div class="mb-4"><label class="form-label fw-semibold" for="message">Order Notes</label><textarea class="form-control" id="message" name="message" rows="4" placeholder="Add delivery details or special requirements."><?= htmlspecialchars($form['message'], ENT_QUOTES, 'UTF-8'); ?></textarea></div><button class="btn btn-warning btn-lg" type="submit"><i class="fas fa-paper-plane me-2"></i>Send Quote Request</button></form></div></div></div></div></section>
<?php include 'includes/footer.php'; ?>
