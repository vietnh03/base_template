<?php

namespace App\AppMain\Domain\Auth\Services;

use App\AppMain\Domain\Auth\Repositories\AdminAuthRepository;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    protected AdminAuthRepository $adminAuthRepository;

    public function __construct(AdminAuthRepository $adminAuthRepository)
    {
        $this->adminAuthRepository = $adminAuthRepository;
    }

    /**
     * Authenticate admin and create token
     */
    public function login(string $email, string $password, string $ip): array
    {
        // Find admin by email
        $admin = $this->adminAuthRepository->findByEmail($email);

        // Validate credentials
        if (!$admin || !Hash::check($password, $admin->password)) {
            throw new \Exception('Invalid credentials');
        }

        // Check if admin is active
        if (!$admin->isActive()) {
            throw new \Exception('Account is inactive');
        }

        // Create token
        $token = $admin->createToken('admin-token')->plainTextToken;

        // Update last login info
        $this->adminAuthRepository->updateLastLogin($admin, $ip);

        return [
            'admin' => $admin,
            'token' => $token,
        ];
    }

    /**
     * Logout admin (revoke current token)
     */
    public function logout($currentToken): bool
    {
        return $this->adminAuthRepository->revokeToken($currentToken);
    }

    /**
     * Logout admin from all devices
     */
    public function logoutAll($admin): void
    {
        $this->adminAuthRepository->revokeAllTokens($admin);
    }
}
