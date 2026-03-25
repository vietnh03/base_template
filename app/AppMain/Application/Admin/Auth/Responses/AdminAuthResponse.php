<?php

namespace App\AppMain\Application\Admin\Auth\Responses;

class AdminAuthResponse
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    public $permissions;
    public $lastLoginAt;
    public $token;
    public $tokenType;

    /**
     * Create response from admin model and token
     */
    public static function fromLoginResult($admin, ?string $token = null): self
    {
        $dto = new self();

        $dto->id = $admin->id;
        $dto->name = $admin->name;
        $dto->email = $admin->email;
        $dto->role = $admin->role;
        $dto->status = $admin->status;
        $dto->permissions = $admin->permissions;
        $dto->lastLoginAt = $admin->last_login_at;

        // Add token if provided (for login response)
        if ($token) {
            $dto->token = $token;
            $dto->tokenType = 'Bearer';
        }

        // Never expose password, remember_token, last_login_ip

        return $dto;
    }

    /**
     * Create response for profile (no token)
     */
    public static function fromModel($admin): self
    {
        return self::fromLoginResult($admin, null);
    }
}
