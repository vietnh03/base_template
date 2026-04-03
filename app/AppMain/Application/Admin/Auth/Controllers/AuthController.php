<?php

namespace App\AppMain\Application\Admin\Auth\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Application\Admin\Auth\Requests\LoginRequest;
use App\AppMain\Application\Admin\Auth\Requests\ForgotPasswordRequest;
use App\AppMain\Application\Admin\Auth\Requests\ResetPasswordRequest;
use App\AppMain\Application\Admin\Auth\Responses\AdminAuthResponse;
use App\AppMain\Domain\Auth\Services\AdminAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AdminAuthService $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    /**
     * Admin login
     */
    public function login(LoginRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            // Login via service
            $result = $this->adminAuthService->login(
                $request->email,
                $request->password,
                $request->ip()
            );

            // Return response with admin info and token
            return AdminAuthResponse::fromLoginResult($result['admin'], $result['token']);
        }, 'Login successful', 'Login failed');
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            return $this->adminAuthService->sendResetLink($request->email);
        });
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            return $this->adminAuthService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );
        });
    }

    /**
     * Get authenticated admin profile
     */
    public function me(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $admin = auth('admin')->user();

            // Return response without token
            return AdminAuthResponse::fromModel($admin);
        }, 'Profile retrieved successfully', 'Failed to retrieve profile');
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            // Logout via service
            $this->adminAuthService->logout($request->user('admin')->currentAccessToken());

            return ['message' => 'Logged out successfully'];
        }, 'Logout successful', 'Logout failed');
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            // Logout from all devices via service
            $this->adminAuthService->logoutAll($request->user('admin'));

            return ['message' => 'Logged out from all devices successfully'];
        }, 'Logout from all devices successful', 'Logout failed');
    }
}
