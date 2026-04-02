<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\CmsPage;
use App\Models\CmsSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiCmsTest extends TestCase
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
    public function it_can_get_page_data()
    {
        $page = CmsPage::create([
            'name' => 'Home Page',
            'slug' => 'home',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/cms/pages/home');

        $response->assertStatus(200)
            ->assertJson(['status' => 1]);
    }

    /** @test */
    public function it_can_update_cms_section_translation()
    {
        $page = CmsPage::create([
            'name' => 'About Us',
            'slug' => 'about-us',
            'is_active' => true
        ]);

        $section = CmsSection::create([
            'page_id' => $page->id,
            'type' => 'text',
            'sort_order' => 1,
            'is_active' => true
        ]);

        $updateData = [
            'locale' => 'en',
            'content' => [
                'title' => 'Our Mission',
                'body' => 'To deliver the best products.'
            ]
        ];

        $response = $this->withAdminAuth()->putJson("/api/admin/cms/sections/{$section->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'CMS Section translation updated successfully'
            ]);
    }
}
