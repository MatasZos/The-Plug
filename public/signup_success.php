<?php
session_start();
require_once '../includes/header.php';
require_once '../src/common.php';
?>

<link rel="stylesheet" href="../css/signup.css">

<div class="signup-section">
    <h2>Successfully Signed Up</h2>
    <p>Welcome, <?= isset($_SESSION['email']) ? escape($_SESSION['email']) : 'Guest' ?>!</p>
    <a href="index.php" class="btn">Go to Homepage</a>
</div>

<?php
require_once '../includes/footer.php';
?>
