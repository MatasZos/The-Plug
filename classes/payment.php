<?php
// Payment.php

class Payment {
    private int $paymentID;
    private int $orderID;
    private string $paymentMethod;
    private float $amountPaid;
    private string $paymentStatus; 

    public function __construct(int $paymentID, int $orderID, string $paymentMethod, float $amountPaid, string $paymentStatus = "Pending") {
        $this->paymentID = $paymentID;
        $this->orderID = $orderID;
        $this->paymentMethod = $paymentMethod;
        $this->amountPaid = $amountPaid;
        $this->paymentStatus = $paymentStatus;
    }

    public function processPayment(): bool {
        if ($this->paymentStatus === "Pending") {
            $this->paymentStatus = "Paid";
            return true;
        }
        return false;
    }

    public function refund(): bool {
        if ($this->paymentStatus === "Paid") {
            $this->paymentStatus = "Refunded";
            return true;
        }
        return false;
    }

    public function getStatus(): string {
        return $this->paymentStatus;
    }

    public function getPaymentID(): int {
        return $this->paymentID;
    }

    public function getOrderID(): int {
        return $this->orderID;
    }

    public function getPaymentMethod(): string {
        return $this->paymentMethod;
    }

    public function getAmountPaid(): float {
        return $this->amountPaid;
    }
}
?>
