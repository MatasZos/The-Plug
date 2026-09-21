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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
        $orderId = (int) $_POST['order_id'];
        $userId = (int) $_SESSION['user_id'];

        $stmt = $connection->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id");
        $stmt->execute([
            'order_id' => $orderId,
            'user_id' => $userId
        ]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Order not found or access denied.");
        }

        if ($order['status'] !== '📦 Pending') {
            throw new Exception("Refund request not allowed. Order already processed.");
        }
        $update = $connection->prepare("UPDATE orders SET status = '🔄 Refund Requested' WHERE id = :order_id");
        $update->execute(['order_id' => $orderId]);

        header("Location: order_history.php?refund_success=1");
        exit;
    } else {
        throw new Exception("Invalid request.");
    }
} catch (Exception $e) {
    echo "<div class='error-message' style='text-align:center;'>" . escape($e->getMessage()) . "</div>";
    require_once '../includes/footer.php';
    exit;
}
?>
