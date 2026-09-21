<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'common.php';

if (!isset($connection)) {
    exit("No database connection.");
}

$total = $total ?? 0;
$discountCode = $_POST['promo_code'] ?? ($_SESSION['discount_code'] ?? '');
$discountAmount = 0;
$discountApplied = false;
$discountMessage = '';

try {
    if (!empty($discountCode)) {
        $stmt = $connection->prepare(
            "SELECT * FROM discounts WHERE code = :code AND is_active = 1 
             AND (start_date IS NULL OR start_date <= CURDATE()) 
             AND (end_date IS NULL OR end_date >= CURDATE())"
        );
        $stmt->execute(['code' => $discountCode]);
        $discount = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($discount) {
            $discountApplied = true;
            if ($discount['type'] === 'percent') {
                $discountAmount = $total * ($discount['amount'] / 100);
            } elseif ($discount['type'] === 'fixed') {
                $discountAmount = $discount['amount'];
            }

            $_SESSION['discount_code'] = $discount['code'];
            $_SESSION['discount_amount'] = $discountAmount;
            $discountMessage = "Promo code applied!";
        } else {
            $discountMessage = "Invalid promo code.";
            unset($_SESSION['discount_code']);
            unset($_SESSION['discount_amount']);
        }
    } else {
        $saleStmt = $connection->query(
            "SELECT * FROM discounts 
             WHERE is_active = 1 
             AND (start_date IS NULL OR start_date <= CURDATE()) 
             AND (end_date IS NULL OR end_date >= CURDATE())
             ORDER BY id DESC LIMIT 1"
        );
        $sale = $saleStmt->fetch(PDO::FETCH_ASSOC);

        if ($sale) {
            if ($sale['type'] === 'percent') {
                $discountAmount = $total * ($sale['amount'] / 100);
            } elseif ($sale['type'] === 'fixed') {
                $discountAmount = $sale['amount'];
            }

            $_SESSION['discount_code'] = $sale['code'];
            $_SESSION['discount_amount'] = $discountAmount;
        } else {
            unset($_SESSION['discount_code']);
            unset($_SESSION['discount_amount']);
        }
    }
} catch (PDOException $e) {
    $discountMessage = "Discount error: " . $e->getMessage();
}
?>
