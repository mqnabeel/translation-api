<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            // Force JSON response for API routes
            if ($request->is('api/*') || $request->wantsJson()) {
                if ($e instanceof AuthException) {
                    return new JsonResponse([
                        'message' => $e->getMessage()
                    ], 422);
                }

                if ($e instanceof ValidationException) {
                    return new JsonResponse([
                        'message' => 'The given data was invalid.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof AuthenticationException) {
                    return new JsonResponse([
                        'message' => 'Unauthenticated.'
                    ], 401);
                }

                if ($e instanceof NotFoundHttpException) {
                    return new JsonResponse([
                        'message' => 'Resource not found.'
                    ], 404);
                }

                // Handle any other exception
                return new JsonResponse([
                    'message' => 'Server Error',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
} 