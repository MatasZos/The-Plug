<?php
require_once 'Product.php';

class Order {
    private int $orderID;
    private int $userID;
    private array $products; 
    private float $totalAmount;
    private string $status; 

    public function __construct(int $orderID, int $userID, array $products, float $totalAmount, string $status = "Pending") {
        $this->orderID = $orderID;
        $this->userID = $userID;
        $this->products = $products;
        $this->totalAmount = $totalAmount;
        $this->status = $status;
    }

    public function placeOrder(): bool {
        if ($this->status === "Pending") {
            $this->status = "Placed";
            return true;
        }
        return false;
    }

    public function cancelOrder(): bool {
        if ($this->status === "Pending") {
            $this->status = "Cancelled";
            return true;
        }
        return false;
    }

    public function shipOrder(): bool {
        if ($this->status === "Placed") {
            $this->status = "Shipped";
            return true;
        }
        return false;
    }

    public function deliverOrder(): bool {
        if ($this->status === "Shipped") {
            $this->status = "Delivered";
            return true;
        }
        return false;
    }

    public function trackOrder(): string {
        return $this->status;
    }

    public function getOrderID(): int {
        return $this->orderID;
    }

    public function getUserID(): int {
        return $this->userID;
    }

    public function getProducts(): array {
        return $this->products;
    }

    public function getTotalAmount(): float {
        return $this->totalAmount;
    }
}
?>
