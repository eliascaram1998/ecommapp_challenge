<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Product
{
    protected static $filePath = 'app/products.json';
    public $id;
    public $title;
    public $price;
    public $created_at;

    public function __construct(array $data = null)
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i');
    }

    /**
     * Get all the products from a JSON file.
     * 
     * @throws Exception If the JSON file does not exist or cannot be read.
     * @return array The decoded JSON data.
     */
    public static function getAllProducts(): array
    {
        $filePath = storage_path(self::$filePath);

        if (!File::exists($filePath)) {
            throw new Exception('Archivo JSON no disponible');
        }

        $jsonData = File::get($filePath);
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al leer el archivo JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Save a collection of products to a JSON file.
     * 
     * @param Collection $products The collection of products to save.
     * @return int The number of bytes written to the file.
     */
    public static function saveToFile(Collection $products): int
    {
        $data = $products->map(function ($product) {
            return [
                'id' => $product['id'],
                'title' => $product['title'],
                'price' => $product['price'],
                'created_at' => $product['created_at'],
            ];
        })->toArray();
    
        return File::put(storage_path(self::$filePath), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Find a product by its ID.
     * 
     * @param int $productId The ID of the product to find.
     * @return Product|null The product object if found, or null if not found.
     */
    public static function findProductById(int $productId): ?Product
    {
        $data = self::getAllProducts();

        foreach ($data as $item) {
            if ($item['id'] == $productId) {
                return new Product($item);
            }
        }

        return null;
    }

    /**
     * Retrieve a paginated collection of products, optionally filtered by criteria.
     * 
     * @param array $filters Optional filters to apply when retrieving products (e.g., title, price, created_at).
     * @param int $start The starting index for pagination.
     * @param int $limit The maximum number of products to return.
     * @return Collection A collection of products based on the specified filters and pagination.
     */
    public static function index(array $filters = [], int $start = 0, int $limit = 20): Collection
    {
        $data = self::getAllProducts();
        $products = collect(array_map(fn($item) => new Product($item), $data));

        $products = self::addFilters($products, $filters);

        return $products->sortByDesc('id')->slice($start, $limit)->values();
    }

    /**
     * Apply filters to a collection of products.
     * 
     * @param Collection $products The collection of products to filter.
     * @param array $filters The filters to apply (title, price, created_at).
     * @return Collection The filtered collection of products.
     */
    private static function addFilters(Collection $products, array $filters): Collection
    {
        if (isset($filters['title'])) {
            $products = $products->filter(fn($product) => $product->title == $filters['title']);
        }

        if (isset($filters['price'])) {
            $products = $products->filter(fn($product) => $product->price == $filters['price']);
        }

        if (isset($filters['created_at'])) {
            $products = $products->filter(
                fn($product) =>
                Carbon::parse($product->created_at)->isSameDay(Carbon::parse($filters['created_at']))
            );
        }

        return $products;
    }

    /**
     * Update a product's details and save the changes to the JSON file.
     * 
     * @param Product $product The product object to update.
     * @return int The number of bytes written to the file.
     */
    public static function update(Product $product): int
    {
        $products = self::getAllProducts();

        foreach ($products as &$existingProduct) {
            if ($existingProduct['id'] == $product->id) {
                $existingProduct['title'] = $product->title;
                $existingProduct['price'] = $product->price;
                break;
            }
        }

        return self::saveToFile(collect($products));
    }

    /**
     * Delete a product by its ID from the JSON file.
     * 
     * @param int $productId The ID of the product to delete.
     * @return int The number of bytes written to the file after deletion.
     */
    public static function delete(int $productId): int
    {
        $products = self::getAllProducts();

        $filteredProducts = array_filter($products, fn($product) => $product['id'] != $productId);

        if (count($filteredProducts) === count($products)) {
            throw new Exception('Producto no encontrado.');
        }

        return self::saveToFile(collect(array_values($filteredProducts)));
    }

    /**
     * Gets the count of products
     * @param array $filters
     * @return int
     */
    public static function count(array $filters = []): int
    {
        $products = Product::index();

        $products = self::addFilters($products, $filters);

        return $products->count();
    }
}
