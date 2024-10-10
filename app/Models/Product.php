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
        $this->created_at = $data['created_at'] ?? now();
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
        $data = self::loadData();

        $products = collect(array_map(function ($item) {
            return new self($item);
        }, $data));

        $products = self::addFilters($products, $filters);

        return $products->sortByDesc('id')->slice($start, $limit)->values();
    }

    /**
     * Find a product by its ID.
     * 
     * @param int $productId The ID of the product to find.
     * @return Product|null The product object if found, or null if not found.
     */
    public static function findProductById(int $productId): Product|null
    {
        $data = self::loadData();

        $products = array_map(function ($item) {
            return new self($item);
        }, $data);

        foreach ($products as $product) {
            if ($product->id == $productId) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Create a new product and save it to the JSON file.
     * 
     * @param array $productData The data for the new product, including title and price.
     * @return int The number of bytes written to the file after the product creation.
     * @throws Exception If the product could not be created.
     */
    public static function create(array $productData): array
    {
        $products = self::index();

        try {
            $nextId = $products->isEmpty() ? 1 : $products->max('id') + 1;
            $productData['id'] = $nextId;
            $productData['created_at'] = date('Y-m-d H:i');
            $product = new self($productData);
            $products->push($product);
        } catch (Exception $e) {
            throw new Exception('No se pudo crear el producto: ' . $e->getMessage());
        }
        self::saveToFile($products);

        return $productData;
    }

    /**
     * Update a product's details and save the changes to the JSON file.
     * 
     * @param array $productData The data to update, including product_id, title, price, and created_at.
     * @return array
     * @throws Exception If the product could not be found.
     */
    public static function update(array $productData): Product
    {
        $product = self::findProductById($productData['product_id']);
        if (!$product) {
            throw new Exception('Producto no encontrado.');
        }

        if ($product) {
            $product->title = $productData['title'] ?? $product->title;
            $product->price = $productData['price'] ?? $product->price;
        }
        self::updateToFile($product);

        return $product;
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

        $filteredProducts = array_filter($products, function ($product) use ($productId) {
            return $product['id'] != $productId;
        });

        if (count($filteredProducts) === count($products)) {
            throw new Exception('Producto no encontrado.');
        }

        $filteredProducts = array_values($filteredProducts);

        return File::put(storage_path(self::$filePath), json_encode($filteredProducts, JSON_PRETTY_PRINT));
    }

    /**
     * Retrieve all products from a JSON file.
     * 
     * @return array An array of products, or an empty array if the file does not exist.
     */
    private static function getAllProducts(): array
    {
        $path = storage_path(self::$filePath);
        if (File::exists($path)) {
            $jsonData = File::get($path);
            return json_decode($jsonData, true);
        }
        return [];
    }

    /**
     * Update a collection of products to a JSON file.
     * 
     * @param Collection $products The collection of products to save.
     * @return int The number of bytes written to the file.
     */
    private static function updateToFile(Product $product): int
    {
        $products = self::getAllProducts();

        foreach ($products as &$existingProduct) {
            if ($existingProduct['id'] == $product->id) {
                $existingProduct['title'] = $product->title;
                $existingProduct['price'] = $product->price;
                break;
            }
        }

        return File::put(storage_path(self::$filePath), json_encode($products, JSON_PRETTY_PRINT));
    }

    /**
     * Save a collection of products to a JSON file.
     * 
     * @param Collection $products The collection of products to save.
     * @return int The number of bytes written to the file.
     */
    private static function saveToFile(Collection $products): int
    {
        $data = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'created_at' => $product->created_at,
            ];
        })->toArray();

        return File::put(storage_path(self::$filePath), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Load data from a JSON file.
     * 
     * @throws Exception If the JSON file does not exist or cannot be read.
     * @return array The decoded JSON data.
     */
    private static function loadData(): array
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
     * Apply filters to a collection of products.
     * 
     * @param Collection $products The collection of products to filter.
     * @param array $filters The filters to apply (title, price, created_at).
     * @return Collection The filtered collection of products.
     */
    private static function addFilters(Collection $products, array $filters): Collection
    {
        if (isset($filters['title'])) {
            $products = $products->filter(function ($product) use ($filters) {
                return $product->title == $filters['title'];
            });
        }

        if (isset($filters['price'])) {
            $products = $products->filter(function ($product) use ($filters) {
                return $product->price == $filters['price'];
            });
        }

        if (isset($filters['created_at'])) {
            $products = $products->filter(function ($product) use ($filters) {
                return Carbon::parse($product->created_at)->isSameDay(Carbon::parse($filters['created_at']));
            });
        }

        return $products;
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
