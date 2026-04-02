<?php

namespace App\AppMain\Domain\Auth\Services;

use App\AppMain\Domain\Auth\Repositories\UserAuthRepository;
use Illuminate\Support\Facades\Hash;

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

    public function register(string $name, string $email, string $password, string $ip): array
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
}
