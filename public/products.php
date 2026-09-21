<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';
require_once '../classes/ProductManager.php';

try {
    $category = $_GET['category'] ?? '';
    $color = $_GET['color'] ?? '';
    $size = $_GET['size'] ?? '';
    $query = $_GET['query'] ?? '';

    $where = [];
    $params = [];

    if (!empty($query)) {
        $where[] = "(p.name LIKE :query OR p.description LIKE :query)";
        $params['query'] = "%$query%";
    }
    if (!empty($category)) {
        $where[] = "p.category = :category";
        $params['category'] = $category;
    }
    if (!empty($color)) {
        $where[] = "v.color = :color";
        $params['color'] = $color;
    }
    if (!empty($size)) {
        $where[] = "v.size = :size";
        $params['size'] = $size;
    }

    $sql = "SELECT DISTINCT p.* FROM products p LEFT JOIN product_variants v ON p.id = v.product_id";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY p.id DESC";

    $stmt = $connection->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categoryStmt = $connection->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    $colorStmt = $connection->query("SELECT DISTINCT color FROM product_variants WHERE color IS NOT NULL");
    $colors = $colorStmt->fetchAll(PDO::FETCH_ASSOC);

    $sizeStmt = $connection->query("SELECT DISTINCT size FROM product_variants WHERE size IS NOT NULL");
    $sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $error) {
    echo "Error loading products: " . $error->getMessage();
    exit;
}
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/product.css">

<div class="products-wrapper">
    <aside class="sidebar">
        <form method="get" class="filter-form">
            <h3>Filters</h3>
            <label for="category">Category</label>
            <select name="category" id="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $row): ?>
                    <option value="<?= escape($row['category']) ?>" <?= ($category === $row['category']) ? 'selected' : '' ?>>
                        <?= escape($row['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="color">Color</label>
            <select name="color" id="color">
                <option value="">All Colors</option>
                <?php foreach ($colors as $row): ?>
                    <option value="<?= escape($row['color']) ?>" <?= ($color === $row['color']) ? 'selected' : '' ?>>
                        <?= escape($row['color']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="size">Size</label>
            <select name="size" id="size">
                <option value="">All Sizes</option>
                <?php foreach ($sizes as $row): ?>
                    <option value="<?= escape($row['size']) ?>" <?= ($size === $row['size']) ? 'selected' : '' ?>>
                        <?= escape($row['size']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="query" value="<?= escape($query) ?>">
            <button type="submit" class="filter-btn">Apply Filters</button>
        </form>
    </aside>

    <main class="product-results">
        <h2>
            <?php if (!empty($query)): ?>
                Search results for: "<?= escape($query) ?>"
            <?php else: ?>
                Browse Products
            <?php endif; ?>
        </h2>

        <?php if (empty($products)): ?>
            <p class="no-results">No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= escape($product['id']) ?>">
                            <img src="../<?= escape($product['image_url']) ?>" alt="<?= escape($product['name']) ?>">
                            <h3><?= escape($product['name']) ?></h3>
                        </a>
                        <p class="price">€<?= number_format($product['price'], 2) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="../js/theme.js"></script>

<?php require_once '../includes/footer.php'; ?>
