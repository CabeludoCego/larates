<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeatureApiTest extends TestCase
{

    // ****************************************************************
    // ************************** SETUP  ******************************
    // ****************************************************************

    use RefreshDatabase; // **!! DONT RUN ON PRODUCTION !!**

    private User $user;
    private User $admin;

    private function createUser(bool $isAdmin = false): User
    {
        return User::factory()->create([
            'is_admin' => $isAdmin
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->admin = $this->createUser(isAdmin:true);
    }

    // ****************************************************************
    // ********************** TESTS SECTION ***************************
    // ****************************************************************

    /**
     * @test
     */
    public function api_returns_products_list(): void
    {
        $product = Product::factory()->create();
        $response = $this->getJson('/api/products');

        // dd($response);

        $response->assertJson([$product->toArray()]);
    }

    /**
     * @test
     */
    public function api_product_store_successful(): void
    {
        $product = [
            'name' => 'Product 1',
            'price' => 299.99
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(201);
        $response->assertJson($product);
    }

    /**
     * @test
     */
    public function api_product_invalid_store_returns_error(): void
    {
        $product = [
            'name' => 'Product 2',
            'price' => 199.99
        ];
        $response = $this->postJson('/api/products', $product);

        $response->assertStatus(201);
    }
}
