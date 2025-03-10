<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Disable browser testing explicitly
        $this->withoutChrome();
    }

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_create_translation_endpoint()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/translations', [
            'key' => 'new_translation',
            'content' => 'New Content',
            'locale' => 'en',
            'tags' => []
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.key', 'new_translation')
            ->assertJsonPath('data.content', 'New Content');
    }

    public function test_update_translation_endpoint()
    {
        // Test code
    }

    public function test_delete_translation_endpoint()
    {
        // Test code
    }

    // And all other endpoints with auth/unauth cases
} 