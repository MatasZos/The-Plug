<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

if (isset($_GET['id'])) {
    try {
        require_once '../../src/db_connect.php';

        $userId = (int) $_GET['id'];

        if ($_SESSION['user_id'] == $userId) {
            $error = "You cannot delete your own account.";
        } else {
            $deleteStmt = $connection->prepare("DELETE FROM users WHERE id = :id");
            $deleteStmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $deleteStmt->execute();

            header("Location: read_users.php?message=" . urlencode("User #$userId deleted successfully."));
            exit;
        }

    } catch (PDOException $e) {
        echo "<p style='color:red; text-align:center;'>Error deleting user: " . escape($e->getMessage()) . "</p>";
        require_once '../../includes/admin_footer.php';
        exit;
    }
} else {
    $error = "User ID not provided.";
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/form_user.css">

<div class="admin-main">
    <div class="form-container">
        <h2>Delete User</h2>
        <?php if (isset($error)): ?>
            <p style="color:red; text-align:center;"><?= escape($error) ?></p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
