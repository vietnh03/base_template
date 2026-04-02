<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiCatalogTest extends TestCase
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
    public function it_can_list_tags()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/catalog/tags');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_tag()
    {
        $tagData = [
            'status' => 1,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'New Tag',
                    'slug' => 'new-tag'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/catalog/tags', $tagData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Tag created successfully'
            ]);
    }

    /** @test */
    public function it_can_list_categories()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/catalog/categories');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $categoryData = [
            'status' => 1,
            'position' => 1,
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => 'New Category',
                    'slug' => 'new-category',
                    'description' => 'Category description'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/catalog/categories', $categoryData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Category created successfully'
            ]);
    }
}
