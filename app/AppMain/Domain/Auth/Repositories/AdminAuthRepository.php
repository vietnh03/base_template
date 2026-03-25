<?php

namespace App\AppMain\Domain\Auth\Repositories;

use App\Models\Admin;

class AdminAuthRepository
{
    protected Admin $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Update admin's last login info
     */
    public function updateLastLogin(Admin $admin, string $ip): bool
    {
        return $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Revoke all tokens for admin
     */
    public function revokeAllTokens(Admin $admin): void
    {
        $admin->tokens()->delete();
    }

    /**
     * Revoke specific token
     */
    public function revokeToken($token): bool
    {
        return $token->delete();
    }
}
