<?php

namespace App\Services;

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use App\Exceptions\TranslationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function __construct(
        private TranslationRepository $repository
    ) {}

    public function create(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw new TranslationException('Failed to create translation: ' . $e->getMessage());
        }
    }

    public function update(Translation $translation, array $data)
    {
        try {
            return $this->repository->update($translation, $data);
        } catch (\Exception $e) {
            throw new TranslationException('Failed to update translation: ' . $e->getMessage());
        }
    }

    public function search(array $criteria): Collection
    {
        $cacheKey = 'translation_search_'.md5(json_encode($criteria));
        return Cache::remember($cacheKey, 60, function() use ($criteria) {
            return $this->repository->search($criteria);
        });
    }

    public function export(string $locale): array
    {
        return $this->repository->export($locale);
    }

    /**
     * Get all translations with pagination
     */
    public function getAllTranslations(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return Cache::remember('translations_page_'.$page.'_per_'.$perPage, 60, function() use ($page, $perPage) {
            return $this->repository->getAllTranslations($page, $perPage);
        });
    }

    /**
     * Get a specific translation with its relationships
     */
    public function getTranslation(Translation $translation): Translation
    {
        return $this->repository->find($translation);
    }

    /**
     * Delete a specific translation
     */
    public function deleteTranslation(Translation $translation): bool
    {
        return $this->repository->delete($translation);
    }

    /**
     * Create a new tag
     */

    /**
     * Sync tags for a translation
     */
    public function syncTags(Translation $translation, array $tagIds): Translation
    {
        $translation->tags()->sync($tagIds);
        return $translation->fresh('tags');
    }
} 