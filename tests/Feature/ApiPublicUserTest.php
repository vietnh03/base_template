<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiPublicUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize Passport for testing
        Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);

        $this->user = User::factory()->create(['status' => 1]);
        $this->token = $this->user->createToken('test-token')->accessToken;
    }

    protected function withAuth()
    {
        return $this->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @test */
    public function it_can_list_users()
    {
        $response = $this->withAuth()->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'phone' => '0987654321',
            'password' => 'password123',
            'status' => 1
        ];

        $response = $this->withAuth()->postJson('/api/users', $userData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User created successfully'
            ]);
    }

    /** @test */
    public function it_can_show_a_user()
    {
        $response = $this->withAuth()->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'data' => [
                    'id' => $this->user->id,
                    'email' => $this->user->email
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'phone' => '0111222333',
            'password' => 'newpassword123',
            'status' => 1
        ];

        $response = $this->withAuth()->putJson("/api/users/{$this->user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User updated successfully'
            ]);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $otherUser = User::factory()->create();

        $response = $this->withAuth()->deleteJson("/api/users/{$otherUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User deleted successfully'
            ]);
    }
}
