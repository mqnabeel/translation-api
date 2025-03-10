<?php

namespace App\Repositories;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationRepository
{
    private const CACHE_TTL = 86400; // 24 hours
    
    public function create(array $data): Translation
    {
        DB::beginTransaction();
        try {
            $translation = Translation::create([
                'key' => $data['key'],
                'content' => $data['content'],
                'locale' => $data['locale'],
            ]);

            if (!empty($data['tags'])) {
                $this->syncTags($translation, $data['tags']);
            }

            DB::commit();
            $this->clearCache($data['locale']);
            
            return $translation->load('tags');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Translation $translation, array $data): Translation
    {
        DB::beginTransaction();
        try {
            $translation->update(['content' => $data['content']]);

            if (isset($data['tags'])) {
                $this->syncTags($translation, $data['tags']);
            }

            DB::commit();
            $this->clearCache($translation->locale);
            
            return $translation->load('tags');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function search(array $filters): Collection
    {
        $query = Translation::query();

        if (isset($filters['key'])) {
            $query->where('key', 'like', "%{$filters['key']}%");
        }

        if (isset($filters['content'])) {
            $query->where('content', 'like', "%{$filters['content']}%");
        }

        if (isset($filters['locale'])) {
            $query->where('locale', $filters['locale']);
        }

        if (isset($filters['tags'])) {
            $tags = explode(',', $filters['tags']);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            });
        }

        return $query->with('tags')->get();
    }

    public function export(string $locale): array
    {
        return Translation::where('locale', $locale)
            ->get()
            ->mapWithKeys(fn($t) => [$t->key => $t->content])
            ->toArray();
    }

    private function syncTags(Translation $translation, array $tagNames): void
    {
        $tags = collect($tagNames)->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName]);
        });
        
        $translation->tags()->sync($tags->pluck('id'));
    }

    private function clearCache(string $locale): void
    {
        Cache::forget("translations.{$locale}");
    }

    /**
     * Get paginated translations
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Translation::with('tags')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Delete a translation and its relationships
     */
    public function delete(Translation $translation): bool
    {
        DB::beginTransaction();
        try {
            // Detach all tags first
            $translation->tags()->detach();
            
            // Delete the translation
            $deleted = $translation->delete();
            
            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Find a specific translation with its relationships
     */
    public function find(Translation $translation): Translation
    {
        return $translation->load('tags');
    }

    public function getAllTranslations(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return Translation::with('tags')
            ->paginate($perPage, ['*'], 'page', $page);
    }
} 