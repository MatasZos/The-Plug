<?php
// Cart.php

require_once 'Product.php';

class Cart {
    private array $items = [];

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    public function addToCart(Product $product, string $color, string $size): void {
        $key = $this->generateKey($product, $color, $size);
        if (isset($this->items[$key])) {
            $this->items[$key]['quantity']++;
        } else {
            $this->items[$key] = [
                'product' => $product,
                'color' => $color,
                'size' => $size,
                'quantity' => 1
            ];
        }
    }

    public function removeFromCart(Product $product, string $color, string $size): void {
        $key = $this->generateKey($product, $color, $size);
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
    }

    public function viewCart(): array {
        return $this->items;
    }

    public function calculateTotal(): float {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    private function generateKey(Product $product, string $color, string $size): string {
        return $product->getId() . '-' . strtolower($color) . '-' . strtolower($size);
    }
}
?>
