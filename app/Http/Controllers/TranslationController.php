<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use App\Services\TranslationService;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\TranslationException;
use App\Http\Requests\Translation\CreateTranslationRequest;
use App\Http\Requests\Translation\UpdateTranslationRequest;

class TranslationController extends Controller
{
    public function __construct(
        private readonly TranslationService $translationService
    ) {}

    /**
     * Store a new translation
     */
    public function store(CreateTranslationRequest $request): JsonResponse
    {
        try {
            $translation = $this->translationService->create($request->validated());
            
            return response()->json([
                'message' => 'Translation created successfully',
                'data' => [
                    'id' => $translation->id,
                    'key' => $translation->key,
                    'content' => $translation->content,
                    'locale' => $translation->locale,
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                    'tags' => $translation->tags
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified translation.
     */
    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        try {
            $translation = $this->translationService->update($translation, $request->validated());
            
            return response()->json([
                'message' => 'Translation updated successfully',
                'data' => [
                    'id' => $translation->id,
                    'key' => $translation->key,
                    'content' => $translation->content,
                    'locale' => $translation->locale,
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                    'tags' => $translation->tags
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search translations based on criteria
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $translations = $this->translationService->search($request->all());
            
            return response()->json([
                'message' => 'Translations retrieved successfully',
                'data' => [
                    'data' => $translations,
                    'total' => $translations->count()
                ]
            ]);
        } catch (TranslationException $e) {
            return response()->json([
                'message' => 'Failed to search translations',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $translations = $this->translationService->export($request->get('locale', 'en'));
            return response()->json($translations);
        } catch (TranslationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * List all translations with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            
            $translations = $this->translationService->getAllTranslations($page, $perPage);
            
            return response()->json([
                'message' => 'Translations retrieved successfully',
                'data' => $translations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified translation.
     */
    public function show(Translation $translation): JsonResponse
    {
        try {
            $translation = $this->translationService->getTranslation($translation);
            
            return response()->json([
                'message' => 'Translation retrieved successfully',
                'data' => [
                    'id' => $translation->id,
                    'key' => $translation->key,
                    'content' => $translation->content,
                    'locale' => $translation->locale,
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                    'tags' => $translation->tags
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified translation.
     */
    public function destroy(Translation $translation): JsonResponse
    {
        try {
            $this->translationService->deleteTranslation($translation);
            
            return response()->json([
                'message' => 'Translation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete translation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manage tags for a translation
     */
    public function manageTags(Request $request, Translation $translation): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id'
            ]);

            $translation = $this->translationService->syncTags($translation, $validated['tags']);
            
            return response()->json([
                'message' => 'Tags updated successfully',
                'data' => [
                    'id' => $translation->id,
                    'key' => $translation->key,
                    'content' => $translation->content,
                    'locale' => $translation->locale,
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                    'tags' => $translation->tags
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 