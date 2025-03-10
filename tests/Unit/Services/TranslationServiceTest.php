<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TranslationService;
use App\Models\Translation;
use Mockery;
use App\Repositories\TranslationRepository;
use Mockery\MockInterface;

class TranslationServiceTest extends TestCase
{
    private TranslationService $service;
    private MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = \Mockery::mock(TranslationRepository::class);
        $this->service = new TranslationService($this->repository);
    }

    /** @test */
    public function testExportTranslations()
    {
        $this->repository->shouldReceive('export')
            ->with('en')
            ->andReturn([
                'welcome_message' => 'Welcome',
                'goodbye_message' => 'Goodbye'
            ]);

        $result = $this->service->export('en');
        
        $this->assertEquals([
            'welcome_message' => 'Welcome',
            'goodbye_message' => 'Goodbye'
        ], $result);
    }

    public function test_create_translation_with_tags()
    {
        // Arrange
        $translationData = [
            'key' => 'welcome_message',
            'content' => 'Welcome',
            'locale' => 'en',
            'tags' => [1, 2]
        ];
        
        $translation = new Translation();
        $translation->id = 1;
        $translation->key = 'welcome_message';
        $translation->content = 'Welcome';
        $translation->locale = 'en';
        
        $this->repository->shouldReceive('create')
            ->with(Mockery::subset(['key' => 'welcome_message']))
            ->andReturn($translation);
            
        $this->repository->shouldReceive('syncTags')
            ->with($translation, [1, 2])
            ->andReturn($translation);

        // Act
        $result = $this->service->create($translationData);

        // Assert
        $this->assertEquals(1, $result->id);
        $this->assertEquals('welcome_message', $result->key);
    }

    public function test_update_translation()
    {
        // Test code
    }

    public function test_delete_translation()
    {
        // Test code
    }

    public function test_create_translation_with_duplicate_key_fails()
    {
        // Arrange
        $this->repository->shouldReceive('create')
            ->andThrow(new \Exception('Duplicate entry'));

        $this->expectException(\Exception::class);
        
        // Act
        $this->service->create([
            'key' => 'duplicate_key',
            'content' => 'Content',
            'locale' => 'en'
        ]);
    }

    // And all other service methods...
} 