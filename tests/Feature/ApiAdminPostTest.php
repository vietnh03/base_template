<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAdminPostTest extends TestCase
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
    public function it_can_list_post_tags()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/posts/tags');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_post_tag()
    {
        $tagData = [
            'status' => 1,
            'translations' => [
                'en' => [
                    'name' => 'News',
                    'slug' => 'news'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/posts/tags', $tagData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Post tag created successfully'
            ]);
    }

    /** @test */
    public function it_can_list_post_categories()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/posts/categories');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_post_category()
    {
        $categoryData = [
            'status' => 1,
            'position' => 1,
            'translations' => [
                'en' => [
                    'name' => 'Technology',
                    'slug' => 'technology',
                    'description' => 'Tech news'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/posts/categories', $categoryData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Post category created successfully'
            ]);
    }

    /** @test */
    public function it_can_list_posts()
    {
        $response = $this->withAdminAuth()->getJson('/api/admin/posts');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_post()
    {
        $category = PostCategory::create(['status' => 1, 'position' => 1]);
        $tag = PostTag::create(['status' => 1]);

        $postData = [
            'status' => 1,
            'author_id' => $this->admin->id,
            'categories' => [$category->id],
            'tags' => [$tag->id],
            'translations' => [
                'en' => [
                    'name' => 'New Post',
                    'slug' => 'new-post',
                    'content' => 'Post content'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/posts', $postData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Post created successfully'
            ]);
    }

    /** @test */
    public function it_can_update_a_post()
    {
        $post = Post::create([
            'status' => 1,
            'author_id' => $this->admin->id
        ]);

        $updateData = [
            'status' => 0,
            'translations' => [
                'en' => [
                    'name' => 'Updated Post',
                    'slug' => 'updated-post',
                    'content' => 'Updated content'
                ]
            ]
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Post updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        $post = Post::create([
            'status' => 1,
            'author_id' => $this->admin->id
        ]);

        $response = $this->withAdminAuth()->deleteJson("/api/admin/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Post deleted successfully'
            ]);
    }
}
