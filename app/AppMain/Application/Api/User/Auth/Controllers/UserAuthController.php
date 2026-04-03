<?php

namespace App\AppMain\Application\Api\User\Auth\Controllers;

use App\AppMain\Application\Api\User\Auth\Requests\UserRegisterRequest;
use App\AppMain\Application\Api\User\Auth\Requests\LoginRequest;
use App\AppMain\Application\Api\User\Auth\Requests\ForgotPasswordRequest;
use App\AppMain\Application\Api\User\Auth\Requests\ResetPasswordRequest;
use App\AppMain\Application\Api\User\Auth\Responses\UserAuthResponse;
use App\AppMain\Domain\Auth\Services\UserAuthService;
use App\AppMain\Core\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    protected UserAuthService $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function register(UserRegisterRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $result = $this->userAuthService->register(
                $request->name,
                $request->email,
                $request->password,
                $request->ip(),
                $request->phone
            );

            return UserAuthResponse::fromArray($result);
        }, 'Registration successful');
    }

    public function login(LoginRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            $result = $this->userAuthService->login(
                $request->email,
                $request->password,
                $request->ip()
            );

            return UserAuthResponse::fromArray($result);
        }, 'Login successful');
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            return $this->userAuthService->sendResetLink($request->email);
        });
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->baseAction(function () use ($request) {
            return $this->userAuthService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );
        });
    }

    public function me(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            return $request->user();
        });
    }

    public function logout(Request $request)
    {
        return $this->baseAction(function () use ($request) {
            $this->userAuthService->logout($request->user()->token());
            return null;
        }, 'Logged out successfully');
    }
}
