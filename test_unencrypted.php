<?php
require_once "vendor/autoload.php";
require_once "bootstrap.php";

// Creating Product using ProductService
$productName = 'My Product';
$productAddress = 'Test address'; // This address will be encrypted by the ProductService
$productId = $productService->createProduct($productName, $productAddress, false);
echo "Created Product with ID " . $productId . "\n";

// Retrieving Product using ProductService
$product = $productService->getProductById($productId);
if ($product !== null) {
    echo "Retrieved Product with ID " . $product->getId() . " and Address " . $product->getAddress() . "\n";
} else {
    echo "Product with ID " . $productId . " not found.\n";
}
