<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
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


    /**
     * @test
     */
    public function admin_can_see_create_button(): void
    {
        $response = $this->actingAs($this->admin)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Add new product');
    }

     /**
     * @test
     */
    public function non_admin_cannot_see_create_button(): void
    {
        $response = $this->actingAs($this->user)
                        ->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('Add new product');
    }

    /**
     * @test
     */
    public function admin_can_access_product_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/products/create');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function non_admin_cannot_access_product_create_page(): void
    {
        $response = $this->actingAs($this->user)
                        ->get('/products/create');

        $response->assertStatus(403);
    }

}
