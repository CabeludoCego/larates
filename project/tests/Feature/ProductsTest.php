<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsTest extends TestCase
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

    //
    /**
     * SECTION 1: BASIC TESTS
     * @test
     */
    public function homepage_contains_empty_table(): void
    {
        $response = $this->actingAs($this->user)->get('/products');
        $response->assertStatus(200);
        $response->assertSee(__('No products found'));
    }

    /**
     * @test
     */
    public function homepage_contains_not_empty_table(): void
    {
        // $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Produto 1',
            'price' => 145
        ]); // uncommon way
        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee(__('No products found'));

        $response->assertViewHas('products', function ($collection) use ($product) {
            return $collection->contains($product);
        });
    }

    /**
     * @test
    */
    public function paginated_products_doesnt_contain_11th_product(): void
    {
        $products = Product::factory(11)->create();
        $lastProduct = $products->last();

        $response = $this->actingAs($this->user)->get('/products');
        $response->assertOk();

        $response->assertViewHas('products', function ($collection) use ($lastProduct) {
            return !$collection->contains($lastProduct);
        });
    }

    // *******************************************************

    /**
     * SECTION 2: OPERATIONAL TESTS (PRODUCT CRUD)
     * @test
     */
    public function create_product_successful ()
    {
        $product = [
            'name' => 'Product 125',
            'price' => 223
        ];
        $response = $this->actingAs($this->admin)->post('/products', $product);

        $response->assertStatus(302);
        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', $product);

        $lastProduct = Product::latest()->first();

        $this->assertEquals($product['name'], $lastProduct->name);
        $this->assertEquals($product['price'], $lastProduct->price);

    }

    /**
     * @test
     */
    public function edit_product_successful()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get('/products/'. $product->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee('value="' . $product->name . '"', escape:false);
        $response->assertSee('value="' . $product->price . '"', escape:false);

        $response->assertViewHas('product', $product);
    }

    /**
     * @test
     */
    public function product_update_validation_error_redirects_to_form() {
        $product = Product::factory()->create();

        // $response = $this->actingAs($this->admin)->put('/products/'. $product->id . '/edit , [
        $response = $this->actingAs($this->admin)->put('/products/'. $product->id , [

            'name' => '',
            'price' => '',
        ]);


        $response->assertStatus(302);
        // $response->assertSessionHasErrors(['name']);
        $response->assertInvalid(['name', 'price']);


    }


    /**
     * @test
     */
    public function product_delete_successful() {
        $product = Product::factory()->create();

        // $response = $this->actingAs($this->admin)->put('/products/'. $product->id . '/edit , [
        $response = $this->actingAs($this->admin)->delete('/products/'. $product->id);


        $response->assertStatus(302);
        $response->assertRedirect('products');

        $this->assertDatabaseMissing('products', $product->toArray());
        $this->assertDatabaseCount('products', 0);

    }

}
