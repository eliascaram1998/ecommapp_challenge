<?php

namespace App\Services;

use App\Interfaces\BaseInterface;
use App\Models\Product;
use Exception;

class ProductUpdate implements BaseInterface
{
    /**
     * Update a product's details and save the changes to the JSON file.
     * 
     * @param array $productData The data to update, including product_id, title, price, and created_at.
     * @return array $product
     * @throws Exception If the product could not be found.
     */
    public function execute(array $productData)
    {
        $product = Product::findProductById($productData['product_id']);
        if (!$product) {
            throw new Exception('Producto no encontrado.');
        }

        if ($product) {
            $product->title = $productData['title'] ?? $product->title;
            $product->price = $productData['price'] ?? $product->price;
        }

        Product::update($product);

        return $product;
    }
}
