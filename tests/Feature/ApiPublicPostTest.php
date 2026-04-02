<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiPublicPostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_list_public_posts()
    {
        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_list_public_post_categories()
    {
        $response = $this->getJson('/api/posts/categories');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_show_public_post()
    {
        $post = Post::create([
            'status' => 1
        ]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }
}
