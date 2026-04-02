<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAdminUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Admin $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        $this->seed(\Database\Seeders\AdminSeeder::class);

        $this->admin = Admin::where('email', 'admin@example.com')->first();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    protected function withAdminAuth()
    {
        return $this->withHeader('Authorization', 'Bearer ' . $this->token);
    }

    /** @test */
    public function it_can_list_users()
    {
        User::factory(5)->create();

        $response = $this->withAdminAuth()->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1
            ])
            ->assertJsonStructure([
                'data' => [
                    'data', // Corrected from 'items'
                    'current_page'
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber, // Added phone
            'password' => 'password123',
            'status' => 1, // Corrected to integer
        ];

        $response = $this->withAdminAuth()->postJson('/api/admin/users', $userData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User created successfully'
            ]);

        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    /** @test */
    public function it_can_show_a_user()
    {
        $user = User::factory()->create();

        $response = $this->withAdminAuth()->getJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = User::factory()->create();
        $newName = 'Updated Name';

        $response = $this->withAdminAuth()->putJson("/api/admin/users/{$user->id}", [
            'name' => $newName,
            'email' => $user->email,
            'phone' => $user->phone ?? $this->faker->unique()->phoneNumber,
            'password' => 'newpassword123',
            'status' => 1 // Corrected to integer
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User updated successfully'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newName
        ]);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->withAdminAuth()->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'User deleted successfully'
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]); // Changed from assertSoftDeleted
    }
}
