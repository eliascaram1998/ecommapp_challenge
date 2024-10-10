<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ProductControllerTest extends TestCase
{
    use WithoutMiddleware;

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
            'created_at' => now(),
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

        $product = Product::create([
            'title' => 'Test Product',
            'price' => 100,
        ]);

        $response = $this->deleteJson("/products/delete/{$product['id']}");

        $response->assertStatus(200);
        $this->assertEquals('Producto eliminado con éxito', $response->json());
    }
}
