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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_discount'])) {
            $code = escape($_POST['code']);
            $type = escape($_POST['type']);
            $amount = (float) $_POST['amount'];
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $banner_message = !empty($_POST['banner_message']) ? escape($_POST['banner_message']) : null;

            $insertStmt = $connection->prepare(
                "INSERT INTO discounts (code, type, amount, is_active, start_date, end_date, banner_message)
                 VALUES (:code, :type, :amount, 1, :start_date, :end_date, :banner_message)"
            );
            $insertStmt->execute([
                'code' => $code,
                'type' => $type,
                'amount' => $amount,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'banner_message' => $banner_message
            ]);
            $success = "Discount added successfully!";
        }

        if (isset($_POST['toggle_active'])) {
            $discountId = (int) $_POST['discount_id'];
            $newStatus = (int) $_POST['new_status'];

            $toggleStmt = $connection->prepare(
                "UPDATE discounts SET is_active = :is_active WHERE id = :id"
            );
            $toggleStmt->execute([
                'is_active' => $newStatus,
                'id' => $discountId
            ]);
        }
    }

    $discounts = $connection->query("SELECT * FROM discounts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Error: " . escape($e->getMessage()) . "</p>";
    exit;
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/admin_discounts.css">

<div class="admin-main">
    <h1>Manage Discount Codes</h1>

    <?php if (!empty($success)): ?>
        <p class="success-message"><?= escape($success) ?></p>
    <?php endif; ?>

    <div class="form-container" style="margin-bottom: 30px;">
        <h2>Add New Discount</h2>
        <form method="POST">
            <label>Discount Code:</label>
            <input type="text" name="code" required>

            <label>Type:</label>
            <select name="type" required>
                <option value="percent">Percent (%)</option>
                <option value="fixed">Fixed (€)</option>
            </select>

            <label>Amount:</label>
            <input type="number" name="amount" step="0.01" required>

            <label>Start Date (optional):</label>
            <input type="date" name="start_date">

            <label>End Date (optional):</label>
            <input type="date" name="end_date">

            <label>Banner Message (optional):</label>
            <textarea name="banner_message" rows="3" placeholder="Example: 🎉 Black Friday Sale - 30% OFF Everything! 🎉"></textarea>

            <button type="submit" name="add_discount">Add Discount</button>
        </form>
    </div>

    <h2>Current Discounts</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Banner Message</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($discounts as $discount): ?>
                <tr>
                    <td><?= escape($discount['id']) ?></td>
                    <td><?= escape($discount['code']) ?></td>
                    <td><?= ucfirst(escape($discount['type'])) ?></td>
                    <td>
                        <?= $discount['type'] === 'percent' ? escape($discount['amount']) . '%' : '€' . escape($discount['amount']) ?>
                    </td>
                    <td><?= $discount['start_date'] ? escape($discount['start_date']) : '-' ?></td>
                    <td><?= $discount['end_date'] ? escape($discount['end_date']) : '-' ?></td>
                    <td><?= $discount['banner_message'] ? escape($discount['banner_message']) : '-' ?></td>
                    <td><?= $discount['is_active'] ? '✅ Active' : '❌ Inactive' ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="discount_id" value="<?= escape($discount['id']) ?>">
                            <input type="hidden" name="new_status" value="<?= $discount['is_active'] ? 0 : 1 ?>">
                            <button type="submit" name="toggle_active" class="small-btn">
                                <?= $discount['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
