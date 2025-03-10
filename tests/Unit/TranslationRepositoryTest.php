<?php

namespace Tests\Unit;

use App\Models\Translation;
use App\Models\Tag;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TranslationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TranslationRepository();
    }

    public function test_create_translation_with_tags(): void
    {
        $data = [
            'key' => 'test_key',
            'content' => 'Test content',
            'locale' => 'en',
            'tags' => ['mobile', 'web']
        ];

        $translation = $this->repository->create($data);

        $this->assertDatabaseHas('translations', [
            'key' => 'test_key',
            'content' => 'Test content',
            'locale' => 'en'
        ]);

        $this->assertCount(2, $translation->tags);
        $this->assertDatabaseHas('tags', ['name' => 'mobile']);
        $this->assertDatabaseHas('tags', ['name' => 'web']);
    }

    public function test_update_translation(): void
    {
        $translation = Translation::factory()->create();
        $tag = Tag::factory()->create(['name' => 'mobile']);

        $updateData = [
            'content' => 'Updated content',
            'tags' => ['mobile', 'new_tag']
        ];

        $updatedTranslation = $this->repository->update($translation, $updateData);

        $this->assertEquals('Updated content', $updatedTranslation->content);
        $this->assertCount(2, $updatedTranslation->tags);
        $this->assertDatabaseHas('tags', ['name' => 'new_tag']);
    }

    public function test_search_by_multiple_criteria(): void
    {
        Translation::factory()->create([
            'key' => 'welcome_message',
            'content' => 'Welcome',
            'locale' => 'en'
        ]);

        $results = $this->repository->search([
            'key' => 'welcome',
            'locale' => 'en'
        ]);

        $this->assertCount(1, $results);
        $this->assertEquals('welcome_message', $results->first()->key);
    }

    public function test_export_uses_cache(): void
    {
        Translation::factory()->create([
            'key' => 'test_key',
            'content' => 'Test content',
            'locale' => 'en'
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['test_key' => 'Test content']);

        $result = $this->repository->export('en');

        $this->assertEquals(['test_key' => 'Test content'], $result);
    }

    public function test_create_translation_with_tags_and_sync(): void
    {
        // Create test tags
        $tags = Tag::factory()->count(2)->create();
        
        // Test data
        $data = [
            'key' => 'welcome_message',
            'content' => 'Welcome',
            'locale' => 'en',
        ];
        
        // Create translation
        $translation = $this->repository->create($data);
        
        // Sync tags
        $syncedTranslation = $this->repository->syncTags($translation, $tags->pluck('id')->toArray());
        
        // Assertions
        $this->assertEquals('welcome_message', $translation->key);
        $this->assertEquals(2, $syncedTranslation->tags->count());
    }
} 