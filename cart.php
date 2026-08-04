<?php
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'config/database.php';

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, image, price, stock FROM products WHERE status = 1 AND id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $product) {
        $product['quantity'] = min((int) $cart[$product['id']], max(1, (int) $product['stock']));
        $product['subtotal'] = $product['price'] * $product['quantity'];
        $total += $product['subtotal'];
        $items[] = $product;
    }
}
?>

<section class="page-header"><div class="container"><h1>Your Cart</h1><p>Review your selected products before sending an inquiry.</p></div></section>
<section class="py-5"><div class="container">
<?php if (isset($_GET['order_success'])): ?>
    <div class="alert alert-success" role="alert"><strong>Order request sent.</strong> Our team will contact you shortly with availability and final pricing.</div>
<?php endif; ?>
<?php if ($items): ?>
    <form action="cart-action.php" method="post">
        <input type="hidden" name="action" value="update"><input type="hidden" name="return_to" value="cart.php">
        <div class="table-responsive"><table class="table align-middle bg-white shadow-sm"><thead class="table-light"><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr></thead><tbody>
        <?php foreach ($items as $item): ?><tr><td><div class="d-flex align-items-center gap-3"><img src="<?= htmlspecialchars($item['image']); ?>" alt="" width="70" height="60" style="object-fit:cover"><strong><?= htmlspecialchars($item['name']); ?></strong></div></td><td>Rs. <?= number_format($item['price'], 2); ?></td><td><input class="form-control" type="number" name="quantity[<?= $item['id']; ?>]" value="<?= $item['quantity']; ?>" min="0" max="<?= max(1, $item['stock']); ?>" data-cart-quantity data-price="<?= htmlspecialchars((string) $item['price'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width:90px"></td><td>Rs. <span data-cart-subtotal><?= number_format($item['subtotal'], 2); ?></span></td><td><button class="btn btn-outline-danger btn-sm" name="action" value="remove" formaction="cart-action.php" formmethod="post" onclick="this.form.product_id.value='<?= $item['id']; ?>'">Remove</button></td></tr><?php endforeach; ?>
        </tbody></table></div>
        <input type="hidden" name="product_id" value="">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center"><button class="btn btn-outline-dark" type="submit">Update Cart</button><div class="text-end"><h4>Total: <span class="text-warning">Rs. <span data-cart-total><?= number_format($total, 2); ?></span></span></h4><button class="btn btn-warning" type="submit" data-place-order>Place Order</button></div></div>
    </form>
<?php else: ?>
    <div class="card border-0 shadow-sm text-center p-5"><i class="fas fa-shopping-cart fa-3x text-warning mb-3" aria-hidden="true"></i><h2>Your cart is empty</h2><p class="text-muted mb-4">Browse our products and add the items you need.</p><div><a class="btn btn-warning" href="products.php">Browse Products</a></div></div>
<?php endif; ?>
</div></section>
<?php include 'includes/footer.php'; ?>
