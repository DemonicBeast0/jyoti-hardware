<?php
require_once 'config/database.php';

$transactionUuid = trim($_GET['transaction_uuid'] ?? '');
if ($transactionUuid !== '') {
    $pdo->prepare("UPDATE orders SET payment_status = 'failed' WHERE transaction_uuid = ? AND payment_status = 'pending'")
        ->execute([$transactionUuid]);
}
header('Location: cart.php?payment=failed');
exit;
