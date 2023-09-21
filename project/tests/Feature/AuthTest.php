<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function unauthenticated_user_cant_access_products(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
        $response->assertRedirect('login');

    }


    /**
     * @test
     */
    public function login_redirect_to_product(): void
    {
        User::create([
            'name' => fake()->name(),
            'email' => 'test@example.com',
            'password' => bcrypt('testword')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'testword'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('products');
    }
}
