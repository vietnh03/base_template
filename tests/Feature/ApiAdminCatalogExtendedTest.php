<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attribute;
use App\Models\AttributeFamily;
use App\Models\Category;
use App\Models\Locale;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAdminCatalogExtendedTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Admin $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\AdminSeeder::class);

        // Ensure at least one locale exists
        Locale::firstOrCreate(['code' => 'en'], ['status' => 1, 'name' => 'English']);

        $this->admin = Admin::where('email', 'admin@example.com')->first();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    protected function withAdminAuth()
    {
        return $this->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @test */
    public function it_can_list_attributes()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/catalog/attributes');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_an_attribute()
    {
        $attributeData = [
            'code' => 'color',
            'admin_name' => 'Color',
            'type' => 'select',
            'is_required' => false,
            'is_unique' => false,
            'is_filterable' => true,
            'is_configurable' => true,
            'options' => [
                ['admin_name' => 'Red'],
                ['admin_name' => 'Blue']
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/catalog/attributes', $attributeData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute created successfully'
            ]);
    }

    /** @test */
    public function it_can_update_an_attribute()
    {
        $attribute = Attribute::create([
            'code' => 'size',
            'admin_name' => 'Size',
            'type' => 'select'
        ]);

        $updateData = [
            'admin_name' => 'Updated Size',
            'type' => 'select',
            'is_required' => true
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/catalog/attributes/{$attribute->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_an_attribute()
    {
        $attribute = Attribute::create([
            'code' => 'material',
            'admin_name' => 'Material',
            'type' => 'text'
        ]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/catalog/attributes/{$attribute->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute deleted successfully'
            ]);
    }

    /** @test */
    public function it_can_list_attribute_families()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/catalog/attribute-families');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_an_attribute_family()
    {
        $familyData = [
            'code' => 'default_family',
            'name' => 'Default Family',
            'status' => 1
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/catalog/attribute-families', $familyData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute family created successfully'
            ]);
    }

    /** @test */
    public function it_can_update_an_attribute_family()
    {
        $family = AttributeFamily::create([
            'code' => 'old_family',
            'name' => 'Old Family',
            'status' => 1
        ]);

        $updateData = [
            'name' => 'Updated Family',
            'status' => 0
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/catalog/attribute-families/{$family->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute family updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_an_attribute_family()
    {
        $family = AttributeFamily::create([
            'code' => 'trash_family',
            'name' => 'Trash Family',
            'status' => 1
        ]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/catalog/attribute-families/{$family->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Attribute family deleted successfully'
            ]);
    }

    /** @test */
    public function it_can_update_a_tag()
    {
        $tag = Tag::create(['status' => 1]);
        $tag->translations()->create([
            'locale' => 'en',
            'name' => 'Original Tag',
            'slug' => 'original-tag'
        ]);

        $updateData = [
            'status' => 0,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Updated Tag',
                    'slug' => 'updated-tag'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/catalog/tags/{$tag->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Tag updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_a_tag()
    {
        $tag = Tag::create(['status' => 1]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/catalog/tags/{$tag->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Tag deleted successfully'
            ]);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $category = Category::create(['status' => 1, 'position' => 1]);
        $category->translations()->create([
            'locale' => 'en',
            'name' => 'Original Category',
            'slug' => 'original-category'
        ]);

        $updateData = [
            'status' => 0,
            'position' => 2,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'Updated Category',
                    'slug' => 'updated-category'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/catalog/categories/{$category->id}", $updateData);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('status'));
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $category = Category::create(['status' => 1, 'position' => 1]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/catalog/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('status'));
    }

    /** @test */
    public function it_can_list_products()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/catalog/products');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $family = AttributeFamily::create([
            'code' => 'test_family',
            'name' => 'Test Family',
            'status' => 1
        ]);

        $productData = [
            'sku' => 'test-product',
            'status' => 1,
            'attribute_family_id' => $family->id,
            'flat' => [
                'en' => [
                    'name' => 'Test Product',
                    'price' => 100,
                    'url_key' => 'test-product'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/catalog/products', $productData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Product created successfully'
            ]);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $product = Product::create([
            'sku' => 'update-me',
            'status' => 1
        ]);

        $updateData = [
            'sku' => 'updated-sku',
            'status' => 0,
            'flat' => [
                'en' => [
                    'name' => 'Updated Product',
                    'price' => 150,
                    'url_key' => 'updated-sku'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/catalog/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Product updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::create([
            'sku' => 'delete-me',
            'status' => 1
        ]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/catalog/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Product deleted successfully'
            ]);
    }
}
