<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

if (isset($_GET['id'])) {
    try {
        require_once '../../src/db_connect.php';

        $productId = (int) $_GET['id'];

        $variantDelete = $connection->prepare("DELETE FROM product_variants WHERE product_id = :product_id");
        $variantDelete->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $variantDelete->execute();

        $productDelete = $connection->prepare("DELETE FROM products WHERE id = :id");
        $productDelete->bindValue(':id', $productId, PDO::PARAM_INT);
        $productDelete->execute();

        header("Location: read_products.php?message=" . urlencode("Product #$productId deleted successfully."));
        exit;

    } catch (PDOException $error) {
        echo "<p style='color:red; text-align:center;'>Error deleting product: " . escape($error->getMessage()) . "</p>";
        require_once '../../includes/admin_footer.php';
        exit;
    }
}
?>

<link rel="stylesheet" href="../../css/admin.css">
<link rel="stylesheet" href="../../css/form_product.css">

<div class="admin-main">
    <div class="form-container">
        <h2>Delete Product</h2>
        <p>No product ID provided. Please return to the <a href="read_products.php">products list</a>.</p>
    </div>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
