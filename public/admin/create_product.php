<?php
session_start();
require_once '../../includes/admin_header.php';
require_once '../../includes/admin_sidebar.php';
require_once '../../src/config.php';
require_once '../../src/common.php';

if (!isset($_SESSION['variants'])) {
    $_SESSION['variants'] = [];
}

if (isset($_POST['add_variant'])) {
    $color = escape($_POST['variant_color']);
    $size = escape($_POST['variant_size']);
    $quantity = (int) ($_POST['variant_quantity'] ?? 0);

    if ($color && $size && $quantity > 0) {
        $_SESSION['variants'][] = [
            'color' => $color,
            'size' => $size,
            'quantity' => $quantity
        ];
    }
}

if (isset($_POST['submit_product'])) {
    try {
        require_once '../../src/db_connect.php';

        $new_product = [
            "name" => escape($_POST['name']),
            "price" => escape($_POST['price']),
            "description" => escape($_POST['description']),
            "image_url" => escape($_POST['image_url']),
            "category" => escape($_POST['category'])
        ];

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            "products",
            implode(", ", array_keys($new_product)),
            ":" . implode(", :", array_keys($new_product))
        );

        $stmt = $connection->prepare($sql);
        $stmt->execute($new_product);
        $productId = $connection->lastInsertId();

        foreach ($_SESSION['variants'] as $variant) {
            $stmt = $connection->prepare("INSERT INTO product_variants (product_id, color, size, quantity) VALUES (:product_id, :color, :size, :quantity)");
            $stmt->execute([
                'product_id' => $productId,
                'color' => $variant['color'],
                'size' => $variant['size'],
                'quantity' => $variant['quantity']
            ]);
        }

        unset($_SESSION['variants']);
        $success = escape($new_product['name']) . ' successfully added.';

    } catch (PDOException $error) {
        echo "<p style='color:red; text-align:center;'>Error adding product: " . escape($error->getMessage()) . "</p>";
        require_once '../../includes/admin_footer.php';
        exit;
    }
}
?>

<link rel="stylesheet" href="../../css/form_product.css">
<link rel="stylesheet" href="../../css/admin.css">

<div class="form-container">
    <h2>Add New Product</h2>

    <?php if (isset($success)): ?>
        <p class="success-message"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <h3>Main Product Info</h3>

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Price (€):</label>
        <input type="number" step="0.01" name="price" required>

        <label>Description:</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Image URL:</label>
        <input type="text" name="image_url" required>

        <label>Category:</label>
        <select name="category" required>
            <?php
            $categories = ['Sneakers', 'Hoodie', 'Jacket', 'T-Shirt', 'Sweatshirt', 'Shoes', 'Jersey'];
            foreach ($categories as $cat):
            ?>
                <option value="<?= escape($cat) ?>"><?= escape($cat) ?></option>
            <?php endforeach; ?>
        </select>

        <hr>

        <h3>Add Variant</h3>

        <label>Color:</label>
        <select name="variant_color">
            <option value="">Select Color</option>
            <?php
            $colors = ['Black', 'White', 'Red', 'Blue', 'Grey'];
            foreach ($colors as $color):
            ?>
                <option value="<?= escape($color) ?>"><?= escape($color) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Size:</label>
        <select name="variant_size">
            <option value="">Select Size</option>
            <?php
            $sizes = ['8', '9', '10', '11', 'S', 'M', 'L', 'XL'];
            foreach ($sizes as $size):
            ?>
                <option value="<?= escape($size) ?>"><?= escape($size) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Quantity:</label>
        <input type="number" name="variant_quantity" min="1" placeholder="Stock quantity">

        <button type="submit" name="add_variant" class="add-variant-btn">Add Variant</button>

        <h3>Current Variants</h3>
        <ul class="variant-list">
            <?php if (!empty($_SESSION['variants'])): ?>
                <?php foreach ($_SESSION['variants'] as $variant): ?>
                    <li><?= escape($variant['color']) ?> | <?= escape($variant['size']) ?> | Stock: <?= escape($variant['quantity']) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No variants added yet.</li>
            <?php endif; ?>
        </ul>

        <button type="submit" name="submit_product" class="submit-product-btn">Add Product</button>
    </form>
</div>

<?php require_once '../../includes/admin_footer.php'; ?>
