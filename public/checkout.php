<?php
session_start();
require_once '../includes/header.php';
require_once '../src/config.php';
require_once '../src/db_connect.php';
require_once '../src/common.php';

$discountCode = $_SESSION['discount_code'] ?? null;
$discountType = $_SESSION['discount_type'] ?? null;
$discountValue = $_SESSION['discount_amount_value'] ?? 0;
$discountAmount = 0;
$cartItems = $_SESSION['cart'] ?? [];
$total = 0;

try {
    foreach ($cartItems as $item) {
        $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $total += $product['price'] * $item['quantity'];
        }
    }
    if (!empty($discountCode)) {
        if ($discountType === 'percent') {
            $discountAmount = $total * ($discountValue / 100);
        } elseif ($discountType === 'fixed') {
            $discountAmount = $discountValue;
        }
    }
    $finalTotal = max($total - $discountAmount, 0);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_order'])) {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
            header("Location: sign_in.php");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? "guest@domain.com";
        $isDelivery = isset($_POST['delivery']) && $_POST['delivery'] === "1";
        $fullName = $_POST['full_name'] ?? '';
        $address = $_POST['address'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $pickupEircode = $_POST['pickup_eircode'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? '';
        $deliveryMethod = $isDelivery ? 'Delivery' : 'Pick-Up';
        $status = '📦 Pending';

        $orderInsert = $connection->prepare("
            INSERT INTO orders (user_id, user_email, full_name, address, contact, delivery_method, payment_method, discount_code, total, discount_total, status)
            VALUES (:user_id, :user_email, :full_name, :address, :contact, :delivery_method, :payment_method, :discount_code, :total, :discount_total, :status)
        ");
        $orderInsert->execute([
            'user_id' => $userId,
            'user_email' => $userEmail,
            'full_name' => $fullName,
            'address' => $address,
            'contact' => $contact,
            'delivery_method' => $deliveryMethod,
            'payment_method' => $paymentMethod,
            'discount_code' => $discountCode,
            'total' => $total,
            'discount_total' => $finalTotal,
            'status' => $status
        ]);

        $orderId = $connection->lastInsertId();

        foreach ($cartItems as $item) {
            $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute(['id' => $item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $itemInsert = $connection->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, price, quantity, color, size, image_url)
                    VALUES (:order_id, :product_id, :product_name, :price, :quantity, :color, :size, :image_url)
                ");
                $itemInsert->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'product_name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $item['quantity'],
                    'color' => $item['color'],
                    'size' => $item['size'],
                    'image_url' => $product['image_url']
                ]);

                $variantUpdate = $connection->prepare("
                    UPDATE product_variants
                    SET quantity = GREATEST(quantity - :quantity, 0)
                    WHERE product_id = :product_id AND color = :color AND size = :size
                ");
                $variantUpdate->execute([
                    'quantity' => $item['quantity'],
                    'product_id' => $item['product_id'],
                    'color' => $item['color'],
                    'size' => $item['size']
                ]);
            }
        }

        unset($_SESSION['cart'], $_SESSION['discount_code'], $_SESSION['discount_type'], $_SESSION['discount_amount_value']);

        header("Location: confirmation.php?order_id=$orderId");
        exit;
    }

    $bannerMessage = null;
    $bannerQuery = $connection->query("SELECT banner_message FROM discounts WHERE is_active = 1 AND banner_message IS NOT NULL LIMIT 1");
    $activeBanner = $bannerQuery->fetch(PDO::FETCH_ASSOC);
    if ($activeBanner) {
        $bannerMessage = $activeBanner['banner_message'];
    }

} catch (PDOException $e) {
    echo "<p style='color:red; text-align:center;'>Checkout error: " . escape($e->getMessage()) . "</p>";
    require_once '../includes/footer.php';
    exit;
}
?>

<link rel="stylesheet" href="../css/checkout.css">
<link rel="stylesheet" href="../css/style.css">
<script src="../js/checkout.js" defer></script>
<script src="../js/save_info.js" defer></script>

<div class="checkout-container">
    <h1>Checkout</h1>

    <?php if (!empty($bannerMessage)): ?>
        <div class="banner-message"><?= escape($bannerMessage) ?></div>
    <?php endif; ?>

    <div class="checkout-steps">
        <span class="checkout-step active">Step 1: Address</span> →
        <span class="checkout-step">Step 2: Payment</span> →
        <span class="checkout-step">Step 3: Review</span>
    </div>

    <div class="checkout-tabs">
        <button type="button" id="delivery-tab" class="tab-btn active">Delivery</button>
        <button type="button" id="pickup-tab" class="tab-btn">Pick-Up</button>
    </div>

    <div id="delivery-form" class="checkout-section">
        <form id="delivery-details">
            <input type="text" name="full_name" placeholder="Full Name" value="<?= escape($_SESSION['saved_full_name'] ?? '') ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= escape($_SESSION['saved_address'] ?? '') ?>" required>
            <input type="text" name="contact" placeholder="Contact Number" value="<?= escape($_SESSION['saved_contact'] ?? '') ?>" required>

            <div class="remember-toggle">
                <label class="remember-label">
                    <input type="checkbox" id="save-info">
                    Save Info
                </label>
            </div>

            <button type="button" id="delivery-continue">Save & Continue</button>
        </form>
    </div>

    <div id="pickup-form" class="checkout-section" style="display: none;">
        <form id="pickup-details">
            <input type="text" name="pickup_eircode" placeholder="Pickup Eircode" required>
            <button type="button" id="pickup-continue">Save & Continue</button>
        </form>
    </div>

    <div id="payment-section" class="checkout-section" style="display: none;">
        <form id="payment-details">
            <select name="payment_method" required>
                <option value="">Select Payment Type</option>
                <option value="Visa">Visa</option>
                <option value="MasterCard">MasterCard</option>
                <option value="Revolut">Revolut</option>
                <option value="PayPal">PayPal</option>
            </select>
            <input type="text" name="card_name" placeholder="Cardholder Name" required>
            <input type="text" name="card_number" placeholder="Card Number" required>
            <input type="text" name="expiry" placeholder="MM/YY" required>
            <input type="text" name="cvv" placeholder="CVV" required>
            <button type="button" id="payment-continue">Save & Continue</button>
        </form>
    </div>

    <div id="review-section" class="checkout-section" style="display: none;">
        <h2>Review Your Order</h2>
        <div class="order-summary">
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): 
                    $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
                    $stmt->execute(['id' => $item['product_id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($product):
                ?>
                <div class="review-item">
                    <img src="../<?= escape($product['image_url']) ?>" width="100">
                    <div>
                        <p><strong><?= escape($product['name']) ?></strong></p>
                        <p>Color: <?= escape($item['color']) ?> | Size: <?= escape($item['size']) ?> | Qty: <?= intval($item['quantity']) ?></p>
                        <p>€<?= number_format($product['price'] * $item['quantity'], 2) ?></p>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            <?php endif; ?>

            <hr>
            <p><strong>Subtotal:</strong> €<?= number_format($total, 2) ?></p>
            <?php if ($discountAmount > 0): ?>
                <p><strong>Discount:</strong> -€<?= number_format($discountAmount, 2) ?></p>
            <?php endif; ?>
            <p><strong>Total:</strong> €<?= number_format($finalTotal, 2) ?></p>
        </div>

        <form method="post">
            <input type="hidden" name="finalize_order" value="1">
            <input type="hidden" name="delivery" id="delivery-flag" value="1">
            <input type="hidden" name="full_name" id="full_name_hidden">
            <input type="hidden" name="address" id="address_hidden">
            <input type="hidden" name="contact" id="contact_hidden">
            <input type="hidden" name="pickup_eircode" id="pickup_hidden">
            <input type="hidden" name="payment_method" id="payment_method_hidden">
            <button type="submit" class="confirm-btn">Confirm Checkout</button>
        </form>
    </div>
</div>

<script src="../js/theme.js"></script>
<?php require_once '../includes/footer.php'; ?>
