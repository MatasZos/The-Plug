<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../sign_in.php");
    exit;
}

try {
    require_once '../../src/db_connect.php';

    $productStmt = $connection->query("SELECT COUNT(*) AS count FROM products");
    $productCount = $productStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $userStmt = $connection->query("SELECT COUNT(*) AS count FROM users");
    $userCount = $userStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $orderStmt = $connection->query("SELECT COUNT(*) AS count FROM orders");
    $orderCount = $orderStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    $discountStmt = $connection->query("SELECT COUNT(*) AS count FROM discounts");
    $discountCount = $discountStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

} catch (PDOException $error) {
    echo "<p style='color:red; text-align:center;'>Error fetching counts: " . escape($error->getMessage()) . "</p>";
    exit;
}
?>

<link rel="stylesheet" href="../../css/admin.css">

<div class="admin-main">
    <h1>Admin Dashboard</h1>

    <div class="dashboard-grid">
        <div class="dashboard-box">
            <h3><?= escape($productCount) ?></h3>
            <p>Products</p>
        </div>

        <div class="dashboard-box">
            <h3><?= escape($userCount) ?></h3>
            <p>Users</p>
        </div>

        <div class="dashboard-box">
            <h3><?= escape($orderCount) ?></h3>
            <p>Orders</p>
        </div>

        <div class="dashboard-box">
            <h3><?= escape($discountCount) ?></h3>
            <p>Discount Codes</p>
        </div>
    </div>

    <div class="dashboard-grid" style="margin-top: 30px;">
        <div class="dashboard-box">
            <h3>Manage Products</h3>
            <p>Edit, delete, or add new products.</p>
            <a href="admin_products.php" class="dashboard-btn">Go to Products</a>
        </div>

        <div class="dashboard-box">
            <h3>Manage Users</h3>
            <p>View and manage registered users.</p>
            <a href="admin_users.php" class="dashboard-btn">Go to Users</a>
        </div>

        <div class="dashboard-box">
            <h3>View Orders</h3>
            <p>Track and manage customer orders.</p>
            <a href="admin_orders.php" class="dashboard-btn">Go to Orders</a>
        </div>

        <div class="dashboard-box">
            <h3>Manage Discounts</h3>
            <p>Create, edit, or deactivate promo codes.</p>
            <a href="admin_discounts.php" class="dashboard-btn">Go to Discounts</a>
        </div>
    </div>
</div>

<footer style="text-align: center; padding: 20px; margin-top: 50px; color: #999; font-size: 14px;">
    &copy; 2025 The Plug Admin Panel. All rights reserved.
</footer>
