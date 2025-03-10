<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Cache;
use App\Services\TranslationService;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);
        
        $this->token = $response->json('token');
    }

    #[Test]
    public function it_can_list_translations(): void
    {
        Translation::factory()->count(15)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/translations?per_page=5');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'current_page',
                        'data' => [
                            '*' => [
                                'id',
                                'key',
                                'content',
                                'locale',
                                'created_at',
                                'updated_at',
                                'tags'
                            ]
                        ],
                        'first_page_url',
                        'from',
                        'last_page',
                        'last_page_url',
                        'links',
                        'next_page_url',
                        'path',
                        'per_page',
                        'prev_page_url',
                        'to',
                        'total'
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'per_page' => 5,
                        'current_page' => 1,
                        'total' => 15
                    ]
                ]);
    }

    #[Test]
    public function it_can_create_translation(): void
    {
        $data = [
            'key' => 'welcome_message',
            'content' => 'Welcome to our app',
            'locale' => 'en'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/translations', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Translation created successfully',
                    'data' => [
                        'key' => 'welcome_message',
                        'content' => 'Welcome to our app',
                        'locale' => 'en',
                        'tags' => []
                    ]
                ])
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'key',
                        'content',
                        'locale',
                        'created_at',
                        'updated_at',
                        'tags'
                    ]
                ]);
    }

    #[Test]
    public function it_validates_translation_creation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/translations', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['key', 'content', 'locale'])
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'key',
                        'content',
                        'locale'
                    ]
                ]);

        // Test invalid locale format
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/translations', [
            'key' => 'test',
            'content' => 'test',
            'locale' => 'invalid'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['locale'])
                ->assertJsonStructure([
                    'message',
                    'errors' => [
                        'locale'
                    ]
                ]);
    }

    #[Test]
    public function it_can_show_translation(): void
    {
        $translation = Translation::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Translation retrieved successfully',
                    'data' => [
                        'id' => $translation->id,
                        'key' => $translation->key
                    ]
                ]);
    }

    #[Test]
    public function it_can_update_translation(): void
    {
        $translation = Translation::factory()->create([
            'key' => 'test_key',
            'content' => 'Original content',
            'locale' => 'en'
        ]);

        $data = [
            'content' => 'Updated content'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->putJson("/api/translations/{$translation->id}", $data);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Translation updated successfully',
                    'data' => [
                        'id' => $translation->id,
                        'key' => 'test_key',
                        'content' => 'Updated content',
                        'locale' => 'en'
                    ]
                ])
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'key',
                        'content',
                        'locale',
                        'created_at',
                        'updated_at',
                        'tags'
                    ]
                ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'content' => 'Updated content'
        ]);
    }

    #[Test]
    public function it_can_delete_translation(): void
    {
        $translation = Translation::factory()->create();
        $tag = Tag::factory()->create();
        $translation->tags()->attach($tag);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Translation deleted successfully'
                ]);

        // Verify translation is deleted
        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id
        ]);

        // Verify tag relationship is removed but tag still exists
        $this->assertDatabaseMissing('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $tag->id
        ]);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id
        ]);
    }

    #[Test]
    public function it_can_search_translations(): void
    {
        Translation::factory()->create(['key' => 'welcome_message']);
        Translation::factory()->create(['key' => 'goodbye_message']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/translations/search?key=welcome');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Translations retrieved successfully',
                    'data' => [
                        'total' => 1
                    ]
                ])
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'key',
                                'content',
                                'locale',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'total'
                    ]
                ]);

        $this->assertEquals(1, count($response->json('data.data')));
        $this->assertEquals('welcome_message', $response->json('data.data.0.key'));
    }

    #[Test]
    public function it_can_manage_tags(): void
    {
        $translation = Translation::factory()->create();
        $tag = Tag::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson("/api/translations/{$translation->id}/tags", [
            'tags' => [$tag->id]
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Tags updated successfully',
                    'data' => [
                        'id' => $translation->id,
                        'tags' => [
                            ['id' => $tag->id]
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $tag->id
        ]);
    }

    public function testPerformance()
    {
        Translation::factory()->count(500)->create();

        $start = microtime(true);
        $this->getJson('/api/translations');
        $this->assertLessThan(0.2, microtime(true) - $start);
        
        $start = microtime(true);
        $this->getJson('/api/translations/search?key=test');
        $this->assertLessThan(0.2, microtime(true) - $start);
    }
} 