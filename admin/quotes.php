<?php
require 'auth.php';
require '../config/database.php';
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$notice = $_GET['notice'] ?? '';
$error = '';
$statuses = ['Pending', 'Contacted', 'Completed'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Your session has expired. Please try again.';
    } else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $action = $_POST['action'] ?? '';
        if ($action === 'status' && $id && in_array($_POST['status'] ?? '', $statuses, true)) {
            $pdo->prepare('UPDATE quotes SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]);
            header('Location: quotes.php?notice=Inquiry status updated.'); exit;
        }
        if ($action === 'delete' && $id) {
            $pdo->prepare('DELETE FROM quotes WHERE id = ?')->execute([$id]);
            header('Location: quotes.php?notice=Inquiry deleted.'); exit;
        }
        $error = 'Unable to update this inquiry.';
    }
}
$quotes = $pdo->query('SELECT q.*, p.name AS current_product_name FROM quotes q LEFT JOIN products p ON p.id = q.product_id ORDER BY q.created_at DESC, q.id DESC')->fetchAll();
include 'includes/header.php'; include 'includes/sidebar.php'; include 'includes/navbar.php';
?>
<div class="content"><div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-4"><h2>Customer Inquiries</h2><span class="badge bg-dark fs-6"><?= count($quotes); ?> total</span></div>
<?php if ($notice): ?><div class="alert alert-success"><?= htmlspecialchars($notice); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card shadow-sm"><div class="card-body table-responsive"><table class="table table-hover align-middle"><thead class="table-dark"><tr><th>Received</th><th>Customer</th><th>Product</th><th>Requirements</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php if (!$quotes): ?><tr><td colspan="6" class="text-center text-muted py-4">No inquiries yet.</td></tr><?php endif; ?><?php foreach ($quotes as $quote): ?><tr><td class="small"><?= htmlspecialchars($quote['created_at']); ?></td><td><strong><?= htmlspecialchars($quote['customer_name']); ?></strong><div class="small"><a href="tel:<?= htmlspecialchars($quote['phone']); ?>"><?= htmlspecialchars($quote['phone']); ?></a></div><?php if ($quote['email']): ?><div class="small"><a href="mailto:<?= htmlspecialchars($quote['email']); ?>"><?= htmlspecialchars($quote['email']); ?></a></div><?php endif; ?></td><td><?= htmlspecialchars($quote['current_product_name'] ?: $quote['product_name']); ?><div class="small text-muted">Qty: <?= (int) $quote['quantity']; ?></div></td><td class="small"><?php if ($quote['company']): ?><strong><?= htmlspecialchars($quote['company']); ?></strong><br><?php endif; ?><?= nl2br(htmlspecialchars($quote['message'] ?: 'No additional requirements.')); ?></td><td><form method="post"><input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= $quote['id']; ?>"><select class="form-select form-select-sm" name="status" onchange="this.form.submit()"><?php foreach ($statuses as $status): ?><option value="<?= $status; ?>" <?= $quote['status'] === $status ? 'selected' : ''; ?>><?= $status; ?></option><?php endforeach; ?></select></form></td><td><form method="post" onsubmit="return confirm('Delete this inquiry permanently?');"><input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $quote['id']; ?>"><button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php include 'includes/footer.php'; ?>
