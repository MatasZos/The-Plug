<?php
require_once 'Product.php';
require_once 'SneakerProduct.php';

echo "<h2>Unit Testing Product Class</h2>";

$product = new Product(1, "Denim Hoodie", 150.00, "Premium cotton hoodie", "hoodie.jpg", "Clothing");
echo "<h3>Testing Product:</h3>";
$product->displayProduct();

echo "<h4>Assertions:</h4>";
echo ($product->getName() === "Denim Hoodie") ? "Name Test Passed<br>" : "Name Test Failed<br>";
echo ($product->getPrice() === 150.00) ? "Price Test Passed<br>" : "Price Test Failed<br>";

$product->setPrice(170.00);
echo ($product->getPrice() === 170.00) ? "Set Price Test Passed<br>" : "Set Price Test Failed<br>";

$sneaker = new SneakerProduct(2, "Jordan 4", 300.00, "Popular sneaker", "jordan.jpg", "Sneakers", "Nike", 10);
echo "<br><h3>Testing SneakerProduct:</h3>";
$sneaker->displayProduct();
?>
