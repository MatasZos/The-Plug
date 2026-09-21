<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

try {
    require_once '../../src/db_connect.php';

    $stmt = $connection->query("SELECT id, full_name, email, is_admin, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Error fetching users: " . escape($e->getMessage()) . "</p>";
    require_once '../../includes/admin_footer.php';
    exit;
}

$message = $_GET['message'] ?? null;
?>

<link rel="stylesheet" href="../../css/admin.css">

<div class="admin-main">
    <h2>Users in ThePlug Database</h2>

    <?php if ($message): ?>
        <p class="success-message">✅ <?= escape($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($users)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= escape($user['id']) ?></td>
                        <td><?= escape($user['full_name']) ?></td>
                        <td><?= escape($user['email']) ?></td>
                        <td><?= $user['is_admin'] ? 'Admin' : 'User' ?></td>
                        <td><?= date("F j, Y", strtotime($user['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
