<?php

namespace Tests\Unit;

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use App\Services\TranslationService;
use App\Exceptions\TranslationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Mockery;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Create the mock repository
        $this->mockRepository = Mockery::mock(TranslationRepository::class);
        // Inject the mock into the service
        $this->service = new TranslationService($this->mockRepository);
    }

    public function test_create_translation_success(): void
    {
        $data = [
            'key' => 'test_key',
            'content' => 'Test content',
            'locale' => 'en',
            'tags' => ['tag1', 'tag2']
        ];

        $expectedTranslation = new Translation($data);

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedTranslation);

        $result = $this->service->create($data);

        $this->assertEquals($expectedTranslation, $result);
    }

    public function test_create_translation_throws_exception(): void
    {
        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->expectException(TranslationException::class);

        $this->service->create([
            'key' => 'test_key',
            'content' => 'Test content',
            'locale' => 'en'
        ]);
    }

    public function test_update_translation_success(): void
    {
        $translation = new Translation([
            'key' => 'test_key',
            'content' => 'Old content',
            'locale' => 'en'
        ]);

        $updateData = [
            'content' => 'Updated content',
            'tags' => ['new_tag']
        ];

        $updatedTranslation = new Translation(array_merge(
            $translation->toArray(),
            ['content' => 'Updated content']
        ));

        $this->mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($translation, $updateData)
            ->andReturn($updatedTranslation);

        $result = $this->service->update($translation, $updateData);

        $this->assertEquals($updatedTranslation, $result);
    }

    public function test_search_translations(): void
    {
        // Setup
        $filters = ['locale' => 'en', 'key' => 'welcome'];
        
        $translation = new Translation([
            'id' => 1,
            'key' => 'welcome_message',
            'content' => 'Welcome',
            'locale' => 'en'
        ]);
        
        $expectedResults = new Collection([$translation]);

        $this->mockRepository
            ->shouldReceive('search')
            ->once()
            ->with($filters)
            ->andReturn($expectedResults);

        // Execute
        $result = $this->service->search($filters);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('welcome_message', $result->first()->key);
        $this->assertEquals('Welcome', $result->first()->content);
    }

    public function test_export_translations(): void
    {
        $locale = 'en';
        $expectedExport = [
            'welcome_message' => 'Welcome',
            'goodbye_message' => 'Goodbye'
        ];

        $this->mockRepository
            ->shouldReceive('export')
            ->once()
            ->with($locale)
            ->andReturn($expectedExport);

        $result = $this->service->export($locale);

        $this->assertEquals($expectedExport, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 