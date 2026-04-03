<?php

namespace App\AppMain\Domain\Auth\Services;

use App\AppMain\Domain\Auth\Repositories\AdminAuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    /**
     * Send password reset link to admin
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::broker('admins')->sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception(__($status));
        }

        return __($status);
    }

    /**
     * Reset admin password
     */
    public function resetPassword(string $email, string $token, string $password): string
    {
        $status = Password::broker('admins')->reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'token' => $token,
            ],
            function ($admin, $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception(__($status));
        }

        return __($status);
    }
}
