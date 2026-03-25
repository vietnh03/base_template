<?php

namespace App\AppMain\CMS\Auth;

use App\AppMain\Domain\Auth\Repositories\AdminAuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    protected AdminAuthRepository $adminAuthRepository;

    public function __construct(AdminAuthRepository $adminAuthRepository)
    {
        $this->adminAuthRepository = $adminAuthRepository;
    }

    /**
     * Attempt to authenticate admin and create session
     *
     * @param array $credentials ['email' => string, 'password' => string]
     * @param bool $remember
     * @param string $ip
     * @return array ['success' => bool, 'admin' => Admin|null, 'message' => string]
     */
    public function login(array $credentials, bool $remember = false, string $ip = null): array
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'Email and password are required',
            ];
        }

        // Find admin by email
        $admin = $this->adminAuthRepository->findByEmail($email);

        if (!$admin) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'The provided credentials do not match our records.',
            ];
        }

        // Check if admin is active
        if (!$admin->isActive()) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'Your account has been deactivated. Please contact support.',
            ];
        }

        // Verify password
        if (!Hash::check($password, $admin->password)) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'The provided credentials do not match our records.',
            ];
        }

        // Attempt authentication with Laravel session
        if (!Auth::guard('admin-cms')->attempt(['email' => $email, 'password' => $password], $remember)) {
            return [
                'success' => false,
                'admin' => null,
                'message' => 'Authentication failed.',
            ];
        }

        // Update last login info
        if ($ip) {
            $this->adminAuthRepository->updateLastLogin($admin, $ip);
        }

        return [
            'success' => true,
            'admin' => $admin,
            'message' => 'Login successful',
        ];
    }

    /**
     * Logout admin and invalidate session
     */
    public function logout(): void
    {
        Auth::guard('admin-cms')->logout();
    }
}
