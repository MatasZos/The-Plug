<?php
require_once 'Product.php';
require_once 'ProductVariant.php';

class ProductManager {
    private array $products;

    public function __construct(array $products = []) {
        $this->products = $products;
    }

    public function addProduct(Product $product): void {
        $this->products[] = $product;
    }

    public function getAllProducts(): array {
        return $this->products;
    }

    public function getProductById(int $id): ?Product {
        foreach ($this->products as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }
        return null;
    }

    public function getVariantsByProduct(array $variantData, Product $product): array {
        $variants = [];
        foreach ($variantData as $data) {
            if (
                isset($data['color']) &&
                isset($data['size']) &&
                isset($data['quantity'])
            ) {
                $variants[] = new ProductVariant(
                    $product,
                    $data['color'],
                    $data['size'],
                    (int) $data['quantity']
                );
            }
        }
        return $variants;
    }
}
