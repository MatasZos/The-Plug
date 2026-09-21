<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

try {
    require_once '../../src/db_connect.php';

    if (!isset($_GET['id'])) {
        throw new Exception("User ID not provided.");
    }

    $userId = (int) $_GET['id'];
    $stmt = $connection->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName = escape($_POST['full_name']);
        $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

        $updateStmt = $connection->prepare(
            "UPDATE users SET full_name = :full_name, is_admin = :is_admin WHERE id = :id"
        );
        $updateStmt->execute([
            'full_name' => $fullName,
            'is_admin' => $isAdmin,
            'id' => $userId
        ]);

        header("Location: read_users.php?message=User updated successfully.");
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color:red; text-align:center;'>" . escape($e->getMessage()) . "</p>";
    require_once '../../includes/admin_footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/form_user.css">

<div class="form-container">
    <h2>Update User</h2>

    <form method="POST">
        <label>Email:</label>
        <input type="text" value="<?= escape($user['email']) ?>" disabled>

        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?= escape($user['full_name'] ?? '') ?>" required>

        <label>Role:</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="is_admin" <?= ($user['is_admin'] ? 'checked' : '') ?>> Admin Access
            </label>
        </div>

        <button type="submit">Update User</button>
    </form>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
