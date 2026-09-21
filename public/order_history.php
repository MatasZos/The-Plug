<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit;
}

try {
    $userId = $_SESSION['user_id'];

    $stmt = $connection->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='error-message' style='text-align:center;'>Error loading your orders: " . escape($e->getMessage()) . "</div>";
    require_once '../includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../css/order_history.css">

<div class="order-history-container">
    <h1>Your Order History</h1>

    <?php if (isset($_GET['refund_success'])): ?>
        <p class="success-message" style="text-align:center; color:green;">✅ Refund request submitted successfully!</p>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <p class="no-orders">You have no past orders.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-box">
                <h2>Order #<?= escape($order['id']) ?> - <?= date("F j, Y, g:i a", strtotime($order['created_at'])) ?></h2>

                <?php $hasDiscount = (isset($order['discount_total']) && $order['discount_total'] < $order['total']); ?>

                <?php if ($hasDiscount): ?>
                    <p><strong>Original Total:</strong> <s>€<?= number_format($order['total'], 2) ?></s></p>
                    <p><strong>Discounted Total:</strong> €<?= number_format($order['discount_total'], 2) ?></p>
                    <?php if (!empty($order['discount_code'])): ?>
                        <p><strong>Promo Code Used:</strong> <?= escape($order['discount_code']) ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><strong>Total:</strong> €<?= number_format($order['total'], 2) ?></p>
                <?php endif; ?>

                <p><strong>Status:</strong> <?= escape($order['status']) ?></p>

                <?php
                try {
                    $itemStmt = $connection->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                    $itemStmt->execute(['order_id' => $order['id']]);
                    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "<div class='error-message' style='text-align:center;'>Error loading items: " . escape($e->getMessage()) . "</div>";
                    continue;
                }
                ?>

                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <img src="../<?= escape($item['image_url']) ?>" alt="<?= escape($item['product_name']) ?>">
                        </div>
                        <div class="order-item-info">
                            <strong><?= escape($item['product_name']) ?></strong><br>
                            Size: <?= escape($item['size']) ?> |
                            Color: <?= escape($item['color']) ?><br>
                            Quantity: <?= intval($item['quantity']) ?> |
                            Price: €<?= number_format($item['price'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($order['status'] === '📦 Pending'): ?>
                    <form method="post" action="request_refund.php" style="margin-top: 15px;">
                        <input type="hidden" name="order_id" value="<?= escape($order['id']) ?>">
                        <button type="submit" class="refund-btn">Request Refund</button>
                    </form>
                <?php elseif ($order['status'] === '🔄 Refund Requested'): ?>
                    <p class="info-message" style="margin-top:10px; color:blue;">Refund request submitted, awaiting admin approval.</p>
                <?php elseif ($order['status'] === '❌ Cancelled'): ?>
                    <p class="info-message" style="margin-top:10px; color:red;">Refund accepted. Order cancelled.</p>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="../js/theme.js"></script>
<?php require_once '../includes/footer.php'; ?>
