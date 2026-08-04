<?php
require_once 'config/database.php';
require_once 'includes/esewa.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$encoded = $_GET['data'] ?? '';
$payload = is_string($encoded) ? json_decode(base64_decode($encoded, true) ?: '', true) : null;
$transactionUuid = is_array($payload) ? (string) ($payload['transaction_uuid'] ?? '') : '';

if (!is_array($payload) || !esewa_verify_callback($payload) || $transactionUuid === '') {
    header('Location: cart.php?payment=invalid');
    exit;
}

$statement = $pdo->prepare('SELECT id, amount, payment_status FROM orders WHERE transaction_uuid = ? LIMIT 1');
$statement->execute([$transactionUuid]);
$order = $statement->fetch();
if (!$order || (string) $payload['product_code'] !== esewa_config()['product_code']
    || number_format((float) ($payload['total_amount'] ?? -1), 2, '.', '') !== number_format((float) $order['amount'], 2, '.', '')) {
    header('Location: cart.php?payment=invalid');
    exit;
}

$status = esewa_transaction_status($transactionUuid, number_format((float) $order['amount'], 2, '.', ''));
if (($status['status'] ?? '') !== 'COMPLETE') {
    $pdo->prepare("UPDATE orders SET payment_status = 'failed', response_data = ? WHERE id = ? AND payment_status = 'pending'")
        ->execute([json_encode(['callback' => $payload, 'status_check' => $status]), $order['id']]);
    header('Location: payment-failure.php?transaction_uuid=' . rawurlencode($transactionUuid));
    exit;
}

$pdo->prepare("UPDATE orders SET payment_status = 'paid', response_data = ?, paid_at = NOW() WHERE id = ?")
    ->execute([json_encode(['callback' => $payload, 'status_check' => $status]), $order['id']]);
$_SESSION['cart'] = [];
header('Location: cart.php?payment=success');
exit;
