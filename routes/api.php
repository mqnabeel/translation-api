<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['api', 'auth:api'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Translation Management Routes
    Route::prefix('translations')->group(function () {
        // CRUD Operations
        Route::get('/', [TranslationController::class, 'index'])
            ->name('translations.index');
        Route::post('/', [TranslationController::class, 'store'])
            ->name('translations.store');
        Route::get('/search', [TranslationController::class, 'search'])
            ->name('translations.search');
        Route::get('/export/{locale?}', [TranslationController::class, 'export'])
            ->name('translations.export');

        Route::get('/{translation}', [TranslationController::class, 'show'])
            ->name('translations.show');
        Route::put('/{translation}', [TranslationController::class, 'update'])
            ->name('translations.update');
        Route::delete('/{translation}', [TranslationController::class, 'destroy'])
            ->name('translations.destroy');

        // Tag Management
        Route::get('/tags', [TranslationController::class, 'getTags'])
            ->name('translations.tags');
        Route::post('/{translation}/tags', [TranslationController::class, 'attachTags'])
            ->name('translations.attachTags');
        Route::delete('/{translation}/tags', [TranslationController::class, 'detachTags'])
            ->name('translations.detachTags');
    });

    Route::post('translations/{translation}/tags', [TranslationController::class, 'manageTags']);
});

// Public Export Route (if needed without authentication)
Route::get('translations/public-export/{locale?}', [TranslationController::class, 'publicExport'])
    ->name('translations.publicExport'); 