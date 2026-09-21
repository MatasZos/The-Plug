<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

try {
    require_once '../../src/db_connect.php';

    $stmt = $connection->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    echo "<p style='color:red; text-align:center;'>Error loading products: " . escape($error->getMessage()) . "</p>";
    exit;
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/admin_products.css">

<div class="admin-main">
    <div class="admin-header">
        <h1>Manage Products</h1>
        <a href="create_product.php" class="add-button">+ Add New Product</a>
    </div>

    <div class="product-grid">
        <?php foreach ($products as $product): 
            $imagePath = '../../' . ltrim($product['image_url'], '/');
        ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= escape($imagePath) ?>" alt="<?= escape($product['name']) ?>">
                </div>
                <div class="product-details">
                    <h3><?= escape($product['name']) ?></h3>
                    <p class="price">€<?= number_format($product['price'], 2) ?></p>
                    <p class="category"><?= escape($product['category']) ?></p>
                </div>
                <div class="product-actions">
                    <a href="update_product.php?id=<?= escape($product['id']) ?>" class="edit-btn">Edit</a>
                    <a href="delete_product.php?id=<?= escape($product['id']) ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
