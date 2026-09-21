<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - The Plug</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="admin_products.php">Manage Products</a></li>
            <li><a href="admin_users.php">Manage Users</a></li>
            <li><a href="admin_orders.php">View Orders</a></li>
            <li><a href="../index.php">Back to Website</a></li>
            <li><a href="../sign_out.php">Sign Out</a></li>
        </ul>
    </aside>

    <main class="admin-main">
