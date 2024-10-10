<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    /** @test */
    public function it_can_create_a_product()
    {
        $data = [
            'title' => 'Test Product',
            'price' => 99.99,
        ];

        $createdProduct = Product::create($data);

        $this->assertEquals('Test Product', $createdProduct['title']);
        $this->assertEquals(99.99, $createdProduct['price']);
        Product::delete($createdProduct['id']);
    }
    /** @test */
    public function it_can_update_a_product()
    {
        $createdProduct = Product::create([
            'title' => 'Test Product',
            'price' => 99.99,
        ]);

        $updatedData = [
            'product_id' => $createdProduct['id'],
            'title' => 'Updated Product',
            'price' => 79.99,
        ];

        Product::update($updatedData);

        $updatedProduct = Product::findProductById($createdProduct['id']);

        $this->assertEquals('Updated Product', $updatedProduct->title);
        $this->assertEquals(79.99, $updatedProduct->price);
        Product::delete($createdProduct['id']);
    }
}
