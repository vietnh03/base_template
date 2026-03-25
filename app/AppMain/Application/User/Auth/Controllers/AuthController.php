<?php

namespace App\AppMain\Application\User\Auth\Controllers;

use App\AppMain\Core\Controller;
use App\AppMain\Application\User\Auth\Requests\LoginRequest;
use App\AppMain\Application\User\Auth\Requests\RegisterRequest;
use App\AppMain\Application\User\Auth\Responses\UserAuthResponse;
use App\AppMain\Domain\Auth\Services\UserAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected UserAuthService $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    /**
     * User register
     */
    public function register(RegisterRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $result = $this->userAuthService->register(
                $request->name,
                $request->email,
                $request->password,
                $request->ip()
            );

            return UserAuthResponse::fromLoginResult($result['user'], $result);
        }, 'Registration successful', 'Registration failed');
    }

    /**
     * User login
     */
    public function login(LoginRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $result = $this->userAuthService->login(
                $request->email,
                $request->password,
                $request->ip()
            );

            return UserAuthResponse::fromLoginResult($result['user'], $result);
        }, 'Login successful', 'Login failed');
    }

    /**
     * Get authenticated user profile
     */
    public function me(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $user = auth('api')->user();
            return UserAuthResponse::fromModel($user);
        }, 'Profile retrieved successfully', 'Failed to retrieve profile');
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $this->userAuthService->logout($request->user('api')->token());
            return ['message' => 'Logged out successfully'];
        }, 'Logout successful', 'Logout failed');
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $this->userAuthService->logoutAll($request->user('api'));
            return ['message' => 'Logged out from all devices successfully'];
        }, 'Logout from all devices successful', 'Logout failed');
    }
}
