<?php

namespace App\AppMain\Application\User\Auth\Responses;

use App\AppMain\Core\BaseResponseDTO;

class UserAuthResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $email;
    public $accessToken;
    public $tokenType;
    public $expiresAt;

    public static function fromModel($model): self
    {
        return static::fromArray([
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
        ]);
    }

    public static function fromArray($data): self
    {
        $response = new self();
        $response->id = $data['user']->id ?? ($data['id'] ?? null);
        $response->name = $data['user']->name ?? ($data['name'] ?? null);
        $response->email = $data['user']->email ?? ($data['email'] ?? null);
        $response->accessToken = $data['access_token'] ?? ($data['accessToken'] ?? null);
        $response->tokenType = $data['token_type'] ?? ($data['tokenType'] ?? null);
        $response->expiresAt = $data['expires_at'] ?? ($data['expiresAt'] ?? null);
        return $response;
    }
}
