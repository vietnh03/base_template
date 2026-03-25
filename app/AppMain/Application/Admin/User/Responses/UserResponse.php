<?php

namespace App\AppMain\Application\Admin\User\Responses;

use App\AppMain\Core\BaseResponseDTO;

class UserResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    public $emailVerifiedAt;
    public $createdAt;
    public $updatedAt;

    public static function fromModel($model): self
    {
        $dto = new self();

        $dto->id = $model->id;
        $dto->name = $model->name;
        $dto->email = $model->email;
        $dto->role = $model->role;
        $dto->status = $model->status;
        $dto->emailVerifiedAt = $model->email_verified_at;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        // Never expose password or remember_token

        return $dto;
    }
}
