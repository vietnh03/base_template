<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiSalesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Admin $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\AdminSeeder::class);
        $this->admin = Admin::where('email', 'admin@example.com')->first();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    protected function withAdminAuth()
    {
        return $this->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @test */
    public function it_can_list_orders()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/sales/orders');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_list_invoices()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/sales/invoices');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_list_transactions()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/sales/transactions');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_direct_order()
    {
        $user = User::factory()->create();

        // Create a product manually
        $product = Product::create([
            'sku' => 'test-sku',
            'type' => 'simple',
            'attribute_family_id' => null,
        ]);

        // Add inventory
        ProductInventory::create([
            'product_id' => $product->id,
            'qty' => 100,
        ]);

        $orderData = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ],
            'shipping_address' => [
                'name' => 'John Doe',
                'address' => '123 Test St',
                'city' => 'Hanoi',
                'country' => 'VN',
                'postcode' => '10000',
                'phone' => '0123456789'
            ],
            'billing_address' => [
                'name' => 'John Doe',
                'address' => '123 Test St',
                'city' => 'Hanoi',
                'country' => 'VN',
                'postcode' => '10000',
                'phone' => '0123456789'
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/sales/orders', $orderData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
    }
}
