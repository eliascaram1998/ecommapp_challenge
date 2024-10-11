<?php

namespace App\Services;

use App\Models\Product;
use App\Interfaces\BaseInterface;
use Exception;

class ProductCreate implements BaseInterface
{
    /**
     * Create a new product and save it to the JSON file.
     * 
     * @param array $productData The data for the new product, including title and price.
     * @return array The newly created product.
     * @throws Exception If the product could not be created.
     */
    public function execute(array $productData): array
    {
        $products = Product::getAllProducts();

        $nextId = empty($products) ? 1 : max(array_column($products, 'id')) + 1;

        $productData['id'] = $nextId;
        $productData['created_at'] = date('Y-m-d H:i');

        $products[] = $productData;

        Product::saveToFile(collect($products));

        return $productData;
    }
}
