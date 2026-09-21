<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../sign_in.php");
    exit;
}

try {
    require_once '../../src/db_connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
        $orderId = (int) $_POST['order_id'];
        $action = $_POST['action'];

        $statusMapping = [
            'shipped'         => '🚚 Shipped',
            'pending'         => '📦 Pending',
            'cancel'          => '❌ Cancelled',
            'refund_requested'=> '🔄 Refund Requested',
            'refund_accept'   => '❌ Cancelled',
            'refund_deny'     => '📦 Pending'
        ];

        if (isset($statusMapping[$action])) {
            $newStatus = $statusMapping[$action];
            $updateStmt = $connection->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $updateStmt->execute([
                'status' => $newStatus,
                'id' => $orderId
            ]);
        }
    }

    $selectedStatus = $_GET['status'] ?? 'All';
    $query = "SELECT * FROM orders";
    $params = [];

    if ($selectedStatus !== 'All') {
        $query .= " WHERE status = :status";
        $params['status'] = $selectedStatus;
    }

    $query .= " ORDER BY id DESC";
    $stmt = $connection->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $error) {
    echo "<p style='color:red; text-align:center;'>Error loading orders: " . escape($error->getMessage()) . "</p>";
    exit;
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/view_order.css">

<div class="order-management">
    <h1>Manage Orders</h1>

    <form method="GET" class="filter-form">
        <label for="status">Filter by Status:</label>
        <select name="status" onchange="this.form.submit()">
            <?php
            $statuses = ['All', '🚚 Shipped', '📦 Pending', '❌ Cancelled', '🔄 Refund Requested'];
            foreach ($statuses as $status):
                $selected = ($status === $selectedStatus) ? 'selected' : '';
            ?>
                <option value="<?= escape($status) ?>" <?= $selected ?>><?= escape($status) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="order-grid">
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <h3>Order #<?= escape($order['id']) ?></h3>
                <p><strong>Customer:</strong> <?= escape($order['user_email']) ?></p>
                <p><strong>Delivery:</strong> <?= escape($order['delivery_method']) ?></p>
                <p><strong>Payment:</strong> <?= escape($order['payment_method']) ?></p>
                <p><strong>Total:</strong> €<?= number_format($order['discount_total'] ?? $order['total'], 2) ?></p>
                <p><strong>Status:</strong> <?= escape($order['status']) ?></p>

                <form method="POST" class="order-actions">
                    <input type="hidden" name="order_id" value="<?= escape($order['id']) ?>">

                    <?php if ($order['status'] === '📦 Pending'): ?>
                        <button type="submit" name="action" value="shipped" class="edit-btn">🚚 Ship Order</button>
                        <button type="submit" name="action" value="cancel" class="delete-btn">❌ Cancel Order</button>

                    <?php elseif ($order['status'] === '🔄 Refund Requested'): ?>
                        <button type="submit" name="action" value="refund_accept" class="edit-btn">✅ Accept Refund</button>
                        <button type="submit" name="action" value="refund_deny" class="delete-btn">🚫 Deny Refund</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
