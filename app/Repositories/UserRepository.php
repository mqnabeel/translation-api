<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password,
        ]);

        return $user->fresh();
    }

    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function deleteAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
} 