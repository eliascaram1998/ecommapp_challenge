<?php

namespace App\Services;

use App\Models\Product;
use App\Interfaces\ProductDeleteInterface;
use Exception;

class ProductDelete implements ProductDeleteInterface
{
    /**
     * Delete a product by its ID from the JSON file.
     * 
     * @param int $productId The ID of the product to delete.
     * @return int The number of bytes written to the file after deletion.
     */
    public function execute(int $productId): int
    {
        $products = Product::getAllProducts();

        $filteredProducts = array_filter($products, fn($product) => $product['id'] != $productId);

        if (count($filteredProducts) === count($products)) {
            throw new Exception('Producto no encontrado.');
        }

        return Product::saveToFile(collect(array_values($filteredProducts)));
    }
}
