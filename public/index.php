<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';

try {
    $recommendedStmt = $connection->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
    $recommendedProducts = $recommendedStmt->fetchAll(PDO::FETCH_ASSOC);

    $newestStmt = $connection->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
    $newestProducts = $newestStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    echo "Error loading products: " . $error->getMessage();
    exit;
}
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/index.css">

<div class="slideshow-container">
    <div class="slides fade">
        <img src="../images/banner1.jpg" alt="Banner 1">
    </div>
    <div class="slides fade">
        <img src="../images/banner2.jpg" alt="Banner 2">
    </div>
    <div class="slides fade">
        <img src="../images/banner3.jpg" alt="Banner 3">
    </div>

    <div class="dot-container">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</div>

<div class="homepage-container">
    <div class="recommended-section">
        <h2 class="section-title">Recommended For You</h2>
        <div class="product-grid">
            <?php foreach ($recommendedProducts as $product): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= escape($product['id']) ?>">
                        <img src="../<?= escape($product['image_url']) ?>" alt="<?= escape($product['name']) ?>">
                        <h3><?= escape($product['name']) ?></h3>
                    </a>
                    <p class="price">€<?= number_format($product['price'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="view-more-container">
            <a href="products.php"><button class="view-more-btn">View Other Products</button></a>
        </div>
    </div>

    <div class="recommended-section">
        <h2 class="section-title">Latest Products</h2>
        <div class="product-grid">
            <?php foreach ($newestProducts as $product): ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= escape($product['id']) ?>">
                        <img src="../<?= escape($product['image_url']) ?>" alt="<?= escape($product['name']) ?>">
                        <h3><?= escape($product['name']) ?></h3>
                    </a>
                    <p class="price">€<?= number_format($product['price'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="../js/homepage.js"></script>
<script src="../js/nav.js"></script>
<script src="../js/theme.js"></script>

<?php require_once '../includes/footer.php'; ?>
