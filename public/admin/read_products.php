<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

try {
    require_once '../../src/db_connect.php';

    $stmt = $connection->query("SELECT id, name, price, image_url, description, category FROM products");
    $products = $stmt->fetchAll();
} catch (PDOException $error) {
    echo "<p style='color:red; text-align:center;'>Error loading products: " . escape($error->getMessage()) . "</p>";
    require_once '../../includes/admin_footer.php';
    exit;
}

$message = $_GET['message'] ?? null;
?>

<link rel="stylesheet" href="../../css/view_products.css">

<div class="admin-main">
    <h2>All Products</h2>

    <?php if ($message): ?>
        <p class="success-message"><?= escape($message) ?></p>
    <?php endif; ?>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?= '../../' . ltrim(escape($product['image_url']), '/') ?>" alt="<?= escape($product['name']) ?>">
                <h3><?= escape($product['name']) ?></h3>
                <p class="category"><?= escape($product['category']) ?></p>
                <p class="price">€<?= number_format($product['price'], 2) ?></p>
                <p class="description"><?= escape($product['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
