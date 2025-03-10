<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function register(array $data): array
    {
        try {
            $user = $this->userRepository->create($data);
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (\Exception $e) {
            throw new AuthException('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(array $credentials): array
    {
        try {
            $user = $this->userRepository->findByEmail($credentials['email']);

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                throw new AuthException('Invalid credentials');
            }

            $this->userRepository->deleteAllTokens($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (AuthException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AuthException('Login failed: ' . $e->getMessage());
        }
    }

    public function logout(User $user): void
    {
        try {
            $this->userRepository->deleteCurrentToken($user);
        } catch (\Exception $e) {
            throw new AuthException('Logout failed: ' . $e->getMessage());
        }
    }

    public function updateProfile(User $user, array $data): User
    {
        try {
            if (isset($data['current_password'])) {
                if (!Hash::check($data['current_password'], $user->password)) {
                    throw new AuthException('Current password is incorrect');
                }
            }

            return $this->userRepository->update($user, $data);
        } catch (AuthException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AuthException('Profile update failed: ' . $e->getMessage());
        }
    }
} 