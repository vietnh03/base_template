<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiCartTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize Passport for testing
        \Illuminate\Support\Facades\Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->accessToken;
    }

    protected function withAuth()
    {
        return $this->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @test */
    public function it_can_add_product_to_cart()
    {
        $product = Product::create([
            'sku' => 'cart-product',
            'status' => 1
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'qty' => 10,
        ]);

        $postData = [
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $response = $this->withAuth()->postJson('/api/cart/add', $postData);

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_get_cart()
    {
        $product = Product::create([
            'sku' => 'get-cart-product',
            'status' => 1
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'qty' => 10,
        ]);

        // Add item first
        $this->withAuth()->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->withAuth()->getJson('/api/cart');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.items'));
    }

    /** @test */
    public function it_can_update_cart_item()
    {
        $product = Product::create([
            'sku' => 'update-cart-product',
            'status' => 1
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'qty' => 10,
        ]);

        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'sku' => $product->sku,
            'name' => 'Test Product',
            'price' => 10,
            'base_price' => 10,
            'total' => 10,
            'base_total' => 10
        ]);

        $updateData = ['quantity' => 5];

        $response = $this->withAuth()->putJson("/api/cart/update/{$item->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_remove_cart_item()
    {
        $product = Product::create([
            'sku' => 'remove-cart-product',
            'status' => 1
        ]);

        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'sku' => $product->sku,
            'name' => 'Test Product',
            'price' => 10,
            'base_price' => 10,
            'total' => 10,
            'base_total' => 10
        ]);

        $response = $this->withAuth()->deleteJson("/api/cart/remove/{$item->id}");

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_checkout()
    {
        $product = Product::create([
            'sku' => 'checkout-product',
            'status' => 1
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'qty' => 10,
            'status' => 1
        ]);

        // Add item first
        $this->withAuth()->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->withAuth()->postJson('/api/cart/checkout');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }
}
