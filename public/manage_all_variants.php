<?php
session_start();
require_once '../includes/admin_header.php';
require_once '../includes/admin_sidebar.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';

$db = new Database('localhost', 'root', '', 'theplug_db');
$message = "";

$productStmt = $db->query("SELECT id, name FROM products ORDER BY name");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $color = trim($_POST['color'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($product_id && $color && $size && $quantity >= 0) {
        $db->query(
            "INSERT INTO product_variants (product_id, color, size, quantity) VALUES (?, ?, ?, ?)",
            [$product_id, $color, $size, $quantity]
        );
        $message = "Variant added successfully.";
    } else {
        $message = "Please fill in all fields correctly.";
    }
}

$variantStmt = $db->query(
    "SELECT pv.id, pv.product_id, p.name AS product_name, pv.color, pv.size, pv.quantity
     FROM product_variants pv
     JOIN products p ON pv.product_id = p.id
     ORDER BY pv.product_id, pv.color, pv.size"
);
$variants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../css/admin.css">
<link rel="stylesheet" href="../css/form_product.css">

<div class="form-container">
    <h2>Manage Product Variants</h2>

    <?php if ($message): ?>
        <p class="success-message"><?= escape($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="variant-form">
        <label>Product</label>
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= escape($product['id']) ?>"><?= escape($product['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Color</label>
        <input type="text" name="color" required>

        <label>Size</label>
        <input type="text" name="size" required>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0" required>

        <button type="submit">Add Variant</button>
    </form>

    <h3>All Variants</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Color</th>
                <th>Size</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($variants as $variant): ?>
                <tr>
                    <td><?= escape($variant['id']) ?></td>
                    <td><?= escape($variant['product_name']) ?></td>
                    <td><?= escape($variant['color']) ?></td>
                    <td><?= escape($variant['size']) ?></td>
                    <td><?= escape($variant['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/admin_footer.php'; ?>