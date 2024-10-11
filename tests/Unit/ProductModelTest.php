<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    /** @test */
    public function test_product_constructor_initializes_attributes()
    {
        $data = [
            'id' => 1,
            'title' => 'Sample Product',
            'price' => 99.99,
            'created_at' => date('Y-m-d H:i')
        ];

        $product = new Product($data);

        $this->assertEquals(1, $product->id);
        $this->assertEquals('Sample Product', $product->title);
        $this->assertEquals(99.99, $product->price);
    }
    /** @test */
    public function test_find_product_by_id()
    {
        File::put(storage_path('app/products.json'), json_encode([['id' => 1, 'title' => 'Test Product', 'price' => 100, 'created_at' => date('Y-m-d H:i')]]));

        $product = Product::findProductById(1);

        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->title);
    }
}
