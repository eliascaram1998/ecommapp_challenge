<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\ProductCreate;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ProductControllerTest extends TestCase
{
    use WithoutMiddleware;
    protected ProductCreate $productCreate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productCreate = new ProductCreate();
    }


    /**
     * Test the listAjax method.
     *
     * @return void
     */
    public function test_list_ajax_returns_json_response()
    {
        session(['isAuthenticated' => true]);

        $response = $this->postJson('/products/listAjax');

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    /**
     * Test the store method.
     *
     * @return void
     */
    public function test_store_creates_product()
    {
        session(['isAuthenticated' => true]);

        $data = [
            'title' => 'Test Product',
            'price' => 100,
            'created_at' => date('Y-m-d H:i'),
        ];

        $response = $this->postJson('/products/store', $data);

        $response->assertStatus(200);
        $this->assertEquals('Producto creado con éxito', $response->json());
    }

    /**
     * Test the delete method.
     *
     * @return void
     */
    public function test_delete_removes_product()
    {
        session(['isAuthenticated' => true]);

        $product = $this->productCreate->execute([
            'title' => 'Test Product',
            'price' => 100,
        ]);

        $response = $this->deleteJson("/products/delete/{$product['id']}");

        $response->assertStatus(200);

        $this->assertNull(Product::findProductById($product['id']));
    }
}
