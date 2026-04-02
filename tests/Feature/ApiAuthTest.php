<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        $this->seed(\Database\Seeders\AdminSeeder::class);

        // Initialize Passport for testing
        Artisan::call('passport:client', ['--personal' => true, '--no-interaction' => true]);
    }

    /** @test */
    public function it_can_login_as_admin()
    {
        $response = $this->postJson('/api/admin/auth/login', [
            'email' => 'admin@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'token'
                ]
            ]);

        $this->assertEquals(1, $response->json('status'));
    }

    /** @test */
    public function it_can_get_admin_profile()
    {
        $admin = Admin::where('email', 'admin@example.com')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'data' => [
                    'email' => 'admin@example.com'
                ]
            ]);
    }

    /** @test */
    public function it_can_logout_admin()
    {
        $admin = Admin::where('email', 'admin@example.com')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Logout successful'
            ]);
    }

    /** @test */
    public function it_can_register_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'accessToken'
                ]
            ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_can_login_as_user()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'accessToken'
                ]
            ]);
    }
}
