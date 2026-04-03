<?php

namespace App\AppMain\Domain\Auth\Services;

use App\AppMain\Domain\Auth\Repositories\UserAuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserAuthService
{
    protected UserAuthRepository $userAuthRepository;

    public function __construct(UserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function login(string $email, string $password, string $ip): array
    {
        $user = $this->userAuthRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if ($user->status != 1) {
            throw new \Exception('Account is inactive');
        }

        // Create Passport token
        $tokenResult = $user->createToken('user-token');
        $token = $tokenResult->accessToken;

        // Update last login
        $this->userAuthRepository->updateLastLogin($user, $ip);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ];
    }

    public function logout($currentToken): bool
    {
        return $this->userAuthRepository->revokeToken($currentToken);
    }

    public function logoutAll($user): void
    {
        $this->userAuthRepository->revokeAllTokens($user);
    }

    public function register(string $name, string $email, string $password, string $ip, string $phone): array
    {
        // Check if email already exists
        if ($this->userAuthRepository->findByEmail($email)) {
            throw new \Exception('Email already registered');
        }

        // Create user
        $user = $this->userAuthRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone,
            'status' => 1,
            'email_verified_at' => null,
        ]);

        // Create Passport token
        $tokenResult = $user->createToken('user-token');
        $token = $tokenResult->accessToken;

        // Update last login
        $this->userAuthRepository->updateLastLogin($user, $ip);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
        ];
    }

    /**
     * Send password reset link to user
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::broker('users')->sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception(__($status));
        }

        return __($status);
    }

    /**
     * Reset user password
     */
    public function resetPassword(string $email, string $token, string $password): string
    {
        $status = Password::broker('users')->reset(
            [
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'token' => $token,
            ],
            function ($user, $password) {
                $user->forceFill([
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
