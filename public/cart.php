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
    if (isset($_GET['remove'])) {
        $removeId = $_GET['remove'];
        unset($_SESSION['cart'][$removeId]);
        header("Location: cart.php");
        exit;
    }

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];
        $color = $_POST['color'] ?? '';
        $size = $_POST['size'] ?? '';
        $key = $productId . '-' . $color . '-' . $size;

        $variantStmt = $connection->prepare("SELECT quantity FROM product_variants WHERE product_id = :product_id AND color = :color AND size = :size");
        $variantStmt->execute([
            'product_id' => $productId,
            'color' => $color,
            'size' => $size
        ]);
        $variant = $variantStmt->fetch(PDO::FETCH_ASSOC);

        if ($variant && $variant['quantity'] > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$key])) {
                if ($_SESSION['cart'][$key]['quantity'] < $variant['quantity']) {
                    $_SESSION['cart'][$key]['quantity']++;
                } else {
                    $error = "No more stock available for this variant.";
                }
            } else {
                $_SESSION['cart'][$key] = [
                    'product_id' => $productId,
                    'color' => $color,
                    'size' => $size,
                    'quantity' => 1
                ];
            }

            if (empty($error)) {
                header("Location: cart.php");
                exit;
            }
        } else {
            $error = "Variant out of stock!";
        }
    }

    $cartItems = $_SESSION['cart'] ?? [];
    $total = 0;

    foreach ($cartItems as $item) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $total += $product['price'] * $item['quantity'];
        }
    }

    require_once '../src/discount_handler.php';
    $finalTotal = $total - ($_SESSION['discount_amount'] ?? 0);

    $recommendedStmt = $connection->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
    $recommendedProducts = $recommendedStmt->fetchAll(PDO::FETCH_ASSOC);
    $bannerMessage = null;
    $bannerQuery = $connection->query("SELECT banner_message FROM discounts WHERE is_active = 1 AND banner_message IS NOT NULL LIMIT 1");
    $activeBanner = $bannerQuery->fetch(PDO::FETCH_ASSOC);
        if ($activeBanner) {
        $bannerMessage = $activeBanner['banner_message'];
}


} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Error loading cart: " . $e->getMessage() . "</p>";
    require_once '../includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../css/cart.css">
<link rel="stylesheet" href="../css/style.css">

<div class="cart-container">
    <h2 class="cart-title">Your Shopping Cart</h2>

    <?php if (!empty($bannerMessage)): ?>
        <div class="banner-message">
            <?= escape($bannerMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= escape($error) ?></p>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cartItems as $key => $item): 
                    $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
                    $stmt->execute(['id' => $item['product_id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$product) continue;
                ?>
                <div class="cart-item-box">
                    <img src="../<?= escape($product['image_url']) ?>" alt="<?= escape($product['name']) ?>">
                    <div class="item-details">
                        <h4><?= escape($product['name']) ?></h4>
                        <p>Color: <?= escape($item['color']) ?></p>
                        <p>Size: <?= escape($item['size']) ?></p>
                        <p>Price: €<?= number_format($product['price'], 2) ?></p>
                        <p>Quantity: <?= intval($item['quantity']) ?></p>
                        <a class="remove-btn" href="?remove=<?= escape($key) ?>">Remove</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>

                <form method="post" class="promo-code-form">
                    <input type="text" name="promo_code" placeholder="Enter promo code" value="<?= escape($_SESSION['discount_code'] ?? '') ?>">
                    <button type="submit">Apply</button>
                </form>

                <?php if (!empty($discountMessage)): ?>
                    <p class="error-message"><?= escape($discountMessage) ?></p>
                <?php elseif (!empty($_SESSION['discount_code'])): ?>
                    <p class="success-message">Promo applied: -€<?= number_format($_SESSION['discount_amount'], 2) ?></p>
                <?php endif; ?>

                <div class="total-section">
                    <p>Subtotal: €<?= number_format($total, 2) ?></p>
                    <?php if (!empty($_SESSION['discount_code'])): ?>
                        <p>Discount: -€<?= number_format($_SESSION['discount_amount'], 2) ?></p>
                    <?php endif; ?>
                    <strong>Total: €<?= number_format($finalTotal, 2) ?></strong>
                </div>

                <div class="checkout-buttons">
                    <a href="checkout.php"><button class="checkout-btn">Proceed to Checkout</button></a>
                    <a href="products.php"><button class="continue-btn">Continue Shopping</button></a>
                </div>
            </div>
        </div>

        <div class="recommendation-section">
            <h3>You Might Also Like</h3>
            <div class="recommendation-grid">
                <?php foreach ($recommendedProducts as $rec): ?>
                    <div class="recommendation-card">
                        <a href="product_detail.php?id=<?= escape($rec['id']) ?>">
                            <img src="../<?= escape($rec['image_url']) ?>" alt="<?= escape($rec['name']) ?>">
                            <h4><?= escape($rec['name']) ?></h4>
                            <p class="price">€<?= number_format($rec['price'], 2) ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="../js/theme.js"></script>
<?php require_once '../includes/footer.php'; ?>
