<?php

namespace App\AppMain\Application\User\Auth\Responses;

class UserAuthResponse
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    public $accessToken;
    public $tokenType;
    public $expiresAt;

    public static function fromLoginResult($user, array $tokenData): self
    {
        $dto = new self();

        $dto->id = $user->id;
        $dto->name = $user->name;
        $dto->email = $user->email;
        $dto->role = $user->role;
        $dto->status = $user->status;
        $dto->accessToken = $tokenData['access_token'];
        $dto->tokenType = $tokenData['token_type'];
        $dto->expiresAt = $tokenData['expires_at'];

        return $dto;
    }

    public static function fromModel($user): self
    {
        $dto = new self();

        $dto->id = $user->id;
        $dto->name = $user->name;
        $dto->email = $user->email;
        $dto->role = $user->role;
        $dto->status = $user->status;

        return $dto;
    }
}
