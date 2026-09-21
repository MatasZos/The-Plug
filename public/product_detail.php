<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';
require_once '../classes/Product.php';
require_once '../classes/ProductVariant.php';
require_once '../classes/ProductManager.php';

try {
    $product_id = $_GET['id'] ?? null;
    if (!$product_id) {
        throw new Exception("Product not found.");
    }

    $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $productData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$productData) {
        throw new Exception("Product not found.");
    }

    $product = new Product(
        $productData['id'],
        $productData['name'],
        $productData['price'],
        $productData['description'],
        $productData['image_url'],
        $productData['category']
    );

    $variantStmt = $connection->prepare("SELECT * FROM product_variants WHERE product_id = :id");
    $variantStmt->execute(['id' => $product_id]);
    $variantData = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['color'], $_POST['size'])) {
        $color = $_POST['color'];
        $size = $_POST['size'];
        $quantity = 1;
        $variantKey = $product_id . '-' . $color . '-' . $size;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$variantKey])) {
            $_SESSION['cart'][$variantKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$variantKey] = [
                'product_id' => $product_id,
                'color' => $color,
                'size' => $size,
                'quantity' => $quantity
            ];
        }

        header("Location: cart.php");
        exit;
    }

    $relatedStmt = $connection->prepare("SELECT * FROM products WHERE id != :id ORDER BY RAND() LIMIT 4");
    $relatedStmt->execute(['id' => $product_id]);
    $relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

    $reviewStmt = $connection->prepare(
        "SELECT r.*, u.email FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id ORDER BY r.created_at DESC"
    );
    $reviewStmt->execute(['product_id' => $product_id]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<p class='not-found' style='text-align:center;'>" . escape($e->getMessage()) . "</p>";
    require_once '../includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../css/product_detail.css">
<link rel="stylesheet" href="../css/style.css">

<div class="product-detail-page">
    <div class="product-container">
        <div class="product-image-container">
            <img src="../<?= escape($product->getImageUrl()) ?>" alt="<?= escape($product->getName()) ?>" class="product-image">
        </div>
        <div class="product-info">
            <div class="product-header">
                <h1><?= escape($product->getName()) ?></h1>
                <p class="product-price">€<?= number_format($product->getPrice(), 2) ?></p>
            </div>
            <p class="product-description"><?= nl2br(escape($product->getDescription())) ?></p>

            <form method="post" class="variant-form">
                <input type="hidden" name="product_id" value="<?= escape($product->getId()) ?>">
                <label for="color">Color</label>
                <select name="color" required>
                    <option value="">Select Color</option>
                    <?php
                    $colorStock = [];
                    foreach ($variantData as $variant) {
                        $colorStock[$variant['color']] = ($colorStock[$variant['color']] ?? 0) + $variant['quantity'];
                    }
                    foreach ($colorStock as $color => $qty) {
                        if ($qty > 0) {
                            echo '<option value="' . escape($color) . '">' . escape($color) . ' (Stock: ' . $qty . ')</option>';
                        } else {
                            echo '<option value="' . escape($color) . '" disabled>' . escape($color) . ' (Out of Stock)</option>';
                        }
                    }
                    ?>
                </select>

                <label for="size">Size</label>
                <select name="size" required>
                    <option value="">Select Size</option>
                    <?php
                    foreach ($variantData as $variant) {
                        $size = $variant['size'];
                        $qty = $variant['quantity'];
                        if ($qty > 0) {
                            echo '<option value="' . escape($size) . '">' . escape($size) . ' (Stock: ' . $qty . ')</option>';
                        } else {
                            echo '<option value="' . escape($size) . '" disabled>' . escape($size) . ' (Out of Stock)</option>';
                        }
                    }
                    ?>
                </select>

                <button type="submit" class="add-to-cart">Add to Cart</button>
            </form>
        </div>
    </div>

    <div class="related-section">
        <h2>Related Products</h2>
        <div class="related-grid">
            <?php foreach ($relatedProducts as $related): ?>
                <div class="related-card">
                    <a href="product_detail.php?id=<?= escape($related['id']) ?>">
                        <img src="../<?= escape($related['image_url']) ?>" alt="<?= escape($related['name']) ?>">
                        <h4><?= escape($related['name']) ?></h4>
                        <p class="price">€<?= number_format($related['price'], 2) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="reviews-section">
        <details>
            <summary><h3>Customer Reviews</h3></summary>
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-box">
                        <strong><?= escape($review['email']) ?></strong>
                        <span class="stars"><?= str_repeat("⭐", intval($review['rating'])) ?></span>
                        <p><?= nl2br(escape($review['comment'])) ?></p>
                        <small><?= date("F j, Y", strtotime($review['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="submit_review.php" method="post" class="review-form">
                    <input type="hidden" name="product_id" value="<?= escape($product_id) ?>">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="">Select Rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                        <?php endfor; ?>
                    </select>

                    <label>Comment</label>
                    <textarea name="comment" rows="3" placeholder="Write your review..." required></textarea>
                    <button type="submit">Submit Review</button>
                </form>
            <?php else: ?>
                <p><a href="sign_in.php">Sign in to leave a review.</a></p>
            <?php endif; ?>
        </details>
    </div>
</div>

<script src="../js/theme.js"></script>
<?php require_once '../includes/footer.php'; ?>
