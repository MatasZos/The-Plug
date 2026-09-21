<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';

try {
    $orderId = $_GET['order_id'] ?? null;
    if (!$orderId) {
        throw new Exception("Order not found.");
    }

    $stmt = $connection->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->execute(['id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Order not found.");
    }

    $itemStmt = $connection->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
    $itemStmt->execute(['order_id' => $orderId]);
    $orderItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<div class='error-message' style='text-align:center;'>" . escape($e->getMessage()) . "</div>";
    require_once '../includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../css/confirmation.css">
<link rel="stylesheet" href="../css/style.css">

<div class="confirmation-container">
    <div class="confirmation-box">
        <h1>✅ Order Confirmed</h1>
        <p class="success-message">
            Thank you for your purchase, <strong><?= escape($order['full_name']) ?></strong>!
        </p>
        <p>Your order ID is <strong>#<?= escape($order['id']) ?></strong>. A confirmation email has been sent to <strong><?= escape($order['user_email']) ?></strong>.</p>
        <p>Status: <strong><?= escape($order['status']) ?></strong></p>

        <div class="order-details">
            <h2>Order Summary</h2>
            <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <p><strong><?= escape($item['product_name']) ?></strong></p>
                    <p>Size: <?= escape($item['size']) ?> | Color: <?= escape($item['color']) ?> | Qty: <?= intval($item['quantity']) ?></p>
                    <p class="price">€<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                </div>
            <?php endforeach; ?>
            <hr>

            <?php if (isset($order['discount_total']) && $order['discount_total'] < $order['total']): ?>
                <p class="total"><strong>Original Total:</strong> <s>€<?= number_format($order['total'], 2) ?></s></p>
                <p class="total"><strong>Discounted Total:</strong> €<?= number_format($order['discount_total'], 2) ?></p>
                <?php if (!empty($order['discount_code'])): ?>
                    <p class="discount-code">Promo Code Used: <strong><?= escape($order['discount_code']) ?></strong></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="total"><strong>Total Paid:</strong> €<?= number_format($order['total'], 2) ?></p>
            <?php endif; ?>
        </div>

        <div class="confirmation-links">
            <a href="index.php" class="home-btn">Continue Shopping</a>
            <a href="order_history.php" class="history-btn">View My Orders</a>
        </div>
    </div>
</div>

<script src="../js/theme.js"></script>
<?php require_once '../includes/footer.php'; ?>
