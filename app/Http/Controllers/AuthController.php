<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());
            
            return response()->json([
                'message' => 'Registration successful',
                'user' => $result['user'],
                'token' => $result['token']
            ], 201);
        } catch (AuthException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            
            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'token' => $result['token']
            ]);
        } catch (AuthException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout(auth()->user());
            
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (AuthException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->updateProfile(
                auth()->user(),
                $request->validated()
            );
            
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (AuthException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 