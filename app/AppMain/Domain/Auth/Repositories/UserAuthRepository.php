<?php

namespace App\AppMain\Domain\Auth\Repositories;

use App\Models\User;

class UserAuthRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function updateLastLogin(User $user, string $ip): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeToken($token): bool
    {
        return $token->delete();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
