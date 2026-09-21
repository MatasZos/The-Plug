<?php
require_once 'Product.php';

class ProductVariant {
    private Product $product;
    private string $color;
    private string $size;
    private int $quantity;

    public function __construct(Product $product, string $color, string $size, int $quantity) {
        $this->product = $product;
        $this->color = $color;
        $this->size = $size;
        $this->quantity = $quantity;
    }

    public function getProduct(): Product {
        return $this->product;
    }

    public function getProductId(): int {
        return $this->product->getId();
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getSize(): string {
        return $this->size;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setColor(string $color): void {
        $this->color = $color;
    }

    public function setSize(string $size): void {
        $this->size = $size;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    public function displayVariant(): void {
        echo "<strong>Product:</strong> " . $this->product->getName() . "<br>";
        echo "<strong>Color:</strong> " . $this->getColor() . "<br>";
        echo "<strong>Size:</strong> " . $this->getSize() . "<br>";
        echo "<strong>Quantity:</strong> " . $this->getQuantity() . "<br>";
    }
}
